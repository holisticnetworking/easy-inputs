<?php
/**
 * @package Easy Inputs
 */
/*
Plugin Name: Easy Inputs
Plugin URI: 
Description: A hypothetical WordPress Forms API, meant to replace the Settings API.
Version: 0.1b
Author: Thomas J Belknap
Author URI: http://belknap.biz
License: GPLv2 or later
*/

class EasyInputs {
	// All of these variables will have their values set in the __construct function:
	private $name;
	private $setting;
	private $action;
	private $method;
	private $nonce_base;
	private $validate;
	private $group;
	
	
	/*
	 * open/close:			Create new form and close it.
	 * @var str $id:		An identifier for the input. (default: $this->name)
	 */
	public function open( $id=null ) {
		// return sprintf('<form id="%s" action="%s" method="%s">', $id, $action, $method) . $this->hidden_fields( $this->setting );
		return sprintf(
			'<form id="%s" action="%s" method="%s">%s',
			!empty( $id ) ? $id : $this->name,
			$this->action,
			$this->method,
			$this->hidden_fields( $this->setting )
		);
	}
	public function close() {
		return '</form>';
	}
	
	
	/*
	 * hidden_fields:		For the Settings API, provide the required nonce fields.
	 * @var str $setting:	The Settings API setting
	 */
	public function hidden_fields( $setting ) {
		if( empty( $setting ) ) return;
		$fields	= sprintf( '<input type="hidden" name="option_page" value="%s" /><input type="hidden" name="action" value="update" />', esc_attr( $setting ) );
		$fields	.= $this->nonce();
		return $fields;
	}
	
	
	/*
	 * nonce:			Don't overthink it. Just let WordPress handle creating the nonce.
	 * 					This function returns, rather than outputs, the nonce, in case we
	 * 					need to do something further before output.
	 * @var str $name:	The name we wish to call the nonce by
	 */
	public function nonce( $name=null, $action=null ) {
		return wp_nonce_field( $this->action, $this->name, true, false );
	}
	
	
	/*
	 * group:				Defines a group of inputs, both logically and physically.
	 * 						Logically, this group is associated with a single nonce to
	 * 						which it is bound. Physically, all elements of a group will
	 * 						be displayed together, in a fieldset, if requested.
	 * @var str $name:		The name of our group.
	 * @var arr $inputs:	An array of input declarations.
	 * @var arr $args:		Our array of arguments, see below.
	 *
	 * ARGUMENT FORMAT
	 * $args	= array(
	 * 	  'legend'	=> array(),
	 * 	  'disabled' 	=> false,
	 * 	  'name'		=> null
	 * )
	 */
	public function group( $name=null, $inputs=null, $args=array() ) {
		if( empty( $name ) or empty( $inputs ) or empty( $args ) ) return;
		extract( $args );
		if( empty( $action ) ) $action = plugin_basename( __FILE__ );
		
		// Each group gets its own nonce automatically:
		$result	= ''; // $this->nonce( $name . '_nonce', $action );
		
		// Append our fieldset, if required:
		$result	.= !empty( $fieldset ) ? $this->fieldset_open( $fieldset ) : '';
		// Append each input per it's own function, else the generic input function:
		foreach( $inputs as $key=>$input ) :
			if( is_array( $input ) ) :
				if( !empty( $input['type'] ) && method_exists( 'EasyInputs', $input['type'] ) ) :
					$result	.= $this->$input['type']( $key, $input, $name );
				else :
					$result .= $this->input( $key, $input, $name );
				endif;
			else :
				$result	.= $this->input( $input, null, $name );
			endif;
		endforeach;
		// Close the fieldset:
		$result	.= !empty( $fieldset ) ? $this->fieldset_close() : '';
		return $result;
	}
	
	
	/*
	 * set_group:		Sets the pointer for the current group
	 */
	public function set_group( $group ) {
		$this->group	= $group;
		return true;
	}
	
	
	/*
	 * fieldset_open/close:		Creates a fieldset with optional legend
	 * @var arr $args:			'attrs' array and optional legend info
	 */
	public function fieldset_open( $args ) {
		extract( $args );
		return sprintf( 
			'<fieldset %s>%s', 
			empty( $attr ) ? '' : $this->attrs_to_str( $attrs ), 
			empty( $legend ) ? '' : $this->legend( $legend );
		);
	}
	public function fieldset_close() {
		return '</fieldset>';
	}
	
	
	/*
	 * legend:			Outputs an HTML legend
	 * @var arr @args:	A title and optional 'attr' list
	 */
	public function legend( $args ) {
		extract( $args );
		return sprintf(
			'<legend %s>%s</legend>', 
			$attr	= empty( $attr ) ? '' : $this->attrs_to_str( $attrs ),
			!empty( $title ) && is_string( $title ) ? $title : ucfirst( preg_replace( '|-_|', ' ', $this->group ) ) // Convert group ID
		);
	}
	
	
	/*
	 * THE FIELDS
	 */
	
	/*
	 * input:			The default and also the model for all inputs structure.
	 * @var str $field:	The field name.
	 * @var str $args:	A collection of additional arguments, formatted below.
	 *
	 * ARGUMENTS FORMAT
	 * $args	= array(
	 * 		$attrs	= arr, <-- HTML attributes
	 * 		$value	= str, <-- The value of the field, defaults to blank
	 * 		$type	= str, <-- The HTML Field type (text, checkbox, etc). 
	 * 						   Defaults to 'text'
	 * 		$name	= str, <-- The fully-qualified name attribute, if desired.
	 * 		$group	= str  <-- The group to which this element belongs. 
	 * );
	 */
	public function input( $field=null, $args=array() ) {
		if( !$field ) return;
		extract( $args );
		$group	= !empty( $group ) ? $group : null;
		$attr	= !empty( $attrs ) ? $this->attrs_to_str( $attrs ) : '';
		$value	= !empty( $value ) ? $value : '';
		$type	= !empty( $type ) ? $type : 'text';
		$name	= !empty( $name ) ? $name : $this->field_name( $field, $group );
		
		// Handle creating a label:
		$html_label	= '';
		if( !isset( $label ) ) :
			$html_label = $this->label( $field, $field );
		elseif( is_string( $label ) ) :
			$html_label = $this->label( $label, $field );
		endif;
		
		return sprintf(
			'%s<input id="%s" type="%s" name="%s" %s value="%s" />',
			$html_label,
			$field,
			$type,
			$name,
			$attr,
			$value
		);
	}
	
	
	/*
	 * label:			Create an HTML label
	 */
	public function label( $for=null, $text=null, $attrs=null ) {
		// Bounce bad requests.
		if( empty( $for ) ) return;
		
		return sprintf(
			'<label %s %s>%s</label>', 
			!empty( $for ) ? sprintf( 'for="%s"', $for ) : '', 
			is_array( $attrs ) ? $this->attrs_to_str( $attrs ) : '', 
			!empty( $text ) && is_string( $text ) ? $text : ucfirst( preg_replace( '|-_|', ' ', $for ) ) // Convert fieldname
		);
	}
	
	/*
	 * button:			Create an HTML button
	 */
	public function button( $type='submit', $val="Submit", $args=null ) {
		if( is_array( $args ) ) extract( $args );
		$group	= !empty( $group ) ? $group : null;
		$attr	= !empty( $attrs ) ? $this->attrs_to_str( $attrs ) : '';
		$value	= !empty( $val ) ? $val : '';
		$type	= !empty( $type ) ? $type : 'text';
		$name	= !empty( $name ) ? $name : $this->field_name( $type, $group );
		
		return sprintf(
			'<button id="%s" type="%s" name="%s" %s value="%s">%s</button>',
			$type,
			$type,
			$name,
			$attr,
			$value,
			$value
		);
	}
	
	
	
	/*
	 * Utility functions
	 */
	
	/*
	 * attrs_to_str:		Convert HTML attributes
	 * 						This could stand some security features, but for
	 * 						now, it's barebones. I don't want to get in the
	 * 						way of HTML5 attributes and data attributes by
	 * 						over-thinking security at this stage.
	 */
	public function attrs_to_str( $attrs=null ) {
		if( !is_array( $attrs ) ) return;
		$to_string	= array();
		foreach( $attrs as $key=>$val ) :
			$to_string[]	= sprintf( '%s="%s"', $key, htmlspecialchars( $val ) );
		endforeach;
		return implode( ' ', $to_string );
	}
	
	/*
	 * field_name:			Assigns a valid field name for the given input args
	 * @var str $field:		The field-specific name.
	 */
	public function field_name( $field=null, $group=null ) {
		if( !$field ) return;
		$group	= empty( $group ) ? $this->group : $group;
		$group	= !empty( $group ) ? sprintf( '[%s]', $group ) : '';
		return sprintf( '%s%s[%s]', $this->name, $group, $field);
	}
	
	
	/*
	 * SAVING THE DATA
	 * this function is not ready for showtime. Disregard
	 */
	public function save( $data ) {
		$add	= 'add_' . $this->type;
		$update	= 'update_' . $this->type;
		// Look for our EasyInputs variable name, else move on:
		if( !empty( $data[$this->name] ) ) :
			foreach( $data[$this->name] as $key=>$vals ) :	
				// For our first pass, we're just assuming we're getting groups of data.
				// Nonce check:
				if ( !isset( $data[$key . '_nonce'] ) || !wp_verify_nonce( $data[$key . '_nonce'], $this->nonce_base ) ) return;
				foreach( $vals as $k=>$v ) :
					if( !$add( $key, $value ) ) : $update( $key, $value ); endif;
				endforeach;
			endforeach;
		endif;
		return $data;
	}
	
	
	
	
	
	
	
	/*
	 * Our Constructor Function
	 * $args = array(
	 * 		'name', <-- 
	 * 		'action', <-- The WordPress "action" that forms created with this instance will call.
	 * 		'nonce_base', <-- The base string from which to form our nonce.
	 * 		'validate', <-- Function to which we pass our form data for validation.
	 * 		'setting', <-- For data saved to a WordPress Settings API setting, this will be the name.
	 *		'group' <-- If you wish to save your data into an array, this is the base name of the array.
	 * );
	 */
	// Ready, steady, go:
	public function __construct( $name='EasyInputs', $args=null ) {
		$this->name			= $name;
		$this->setting		= $name . '_ei';
		if( !empty( $args ) ) extract( $args );
		$this->action		= empty( $action ) ? null : $action;
		$this->method		= empty( $method ) ? 'post' : $method;
		$this->nonce_base	= empty( $nonce_base ) ? plugin_basename( __FILE__ ) : $nonce_base;
		$this->validate		= empty( $validate ) ? array( $this, 'validate' ) : $validate;
		$this->group		= empty( $group ) ? null : $group;
		// Register a WordPress setting:
		register_setting( $setting, $name );
	}
}
