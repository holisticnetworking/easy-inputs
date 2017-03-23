<?php
/**
 * @package EasyInputs
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
	/**
	 * @var string $name			The name of the Easy Inputs instance.
	 * @var string $setting		The affected Settings API setting.
	 * @var string $action		The action to send the form data to.
	 * @var string $method		GET, POST, etc.
	 * @var string $nonce_base	The value we will use to base our nonces on.
	 * @var string $validate		Callable validation function.
	 * @var string $group			For data saved as an array, the group name.
	 */
	private $name;
	private $setting;
	private $action;
	private $method;
	private $nonce_base;
	private $validate;
	private $group;
	
	
	/**
	 * Open a form element
	 *
	 * This function will allow you to create the opening <form> tag with attributes.
	 * It should be used in combination with the close() function. This form will also
	 * optionally include WordPress nonce fields, created using the $id param.
	 *
	 * @param string|null $id The name of the form. Also serves as the HTML id tag. Optional
	 *
	 * @return string the opening tag for the form element.
	 */
	public function open( $id=null ) {
		return sprintf(
			'<form id="%s" action="%s" method="%s">%s',
			!empty( $id ) ? $id : $this->name,
			$this->action,
			$this->method,
			$this->hidden_fields( $this->setting )
		);
	}
	/**
	 * Close a form element
	 *
	 * @return string the closing tag for the form element.
	 */
	public function close() {
		return '</form>';
	}
	
	
	
	/**
	 * For the Settings API, provide the required nonce fields.
	 *
	 * @param string $setting The Settings API setting to which this control belongs.
	 *
	 * @return string Nonce fields.
	 */
	public function hidden_fields( $setting ) {
		if( empty( $setting ) ) return;
		$fields	= sprintf( '<input type="hidden" name="option_page" value="%s" /><input type="hidden" name="action" value="update" />', esc_attr( $setting ) );
		$fields	.= $this->nonce();
		return $fields;
	}
	
	
	/**
	 * Return a WP Settings API nonce field.
	 *
	 * Don't overthink it. Just let WordPress handle creating the nonce.
	 * This function returns, rather than outputs, the nonce, in case we
	 * need to do something further before output.
	 *
	 * @param string $name A name from which to create our nonce.
	 * @param string $action The action requiring our nonce.
	 *
	 * @return string the opening tag for the form element.
	 */
	public function nonce( $name=null, $action=null ) {
		return wp_nonce_field( $this->action, $this->name, true, false );
	}
	
	
	/**
	 * Display a group of inputs
	 *
	 * Defines a group of inputs, both logically and physically.
	 * Logically, this group is associated with a single nonce to
	 * which it is bound. Physically, all elements of a group will
	 * be displayed together, in a fieldset, if requested.
	 *
	 * @param string $name The name of our group.
	 * @param array $inputs Array of input arrays.
	 * @param array $args Arguments and attributes to be applied to the group 
	 * container
	 *
	 * @return string A string of HTML including all inputs from $inputs.
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
	 * set_group:		
	 */
	/**
	 * Display a group of inputs
	 *
	 * @param string $group The name of our group.
	 *
	 * @return bool true
	 */
	public function set_group( $group ) {
		$this->group	= $group;
		return true;
	}
	
	
	
	/**
	 * Creates a fieldset opening tag with optional legend
	 *
	 * The legend key of the $args array is identical to the legend() function. The
	 * attrs array contains the same array of HTML attributes as always.
	 *
	 * @param array $args 'attrs' array and optional legend info
	 *
	 * @return string HTML containing the opening tag for a fieldset with optional legend.
	 */
	public function fieldset_open( $args ) {
		extract( $args );
		return sprintf( 
			'<fieldset %s>%s', 
			empty( $attr ) ? '' : $this->attrs_to_str( $attrs ), 
			empty( $legend ) ? '' : $this->legend( $legend )
		);
	}
	/**
	 * Creates a fieldset closing tag
	 *
	 * @return string HTML containing the closing tag for a fieldset.
	 */
	public function fieldset_close() {
		return '</fieldset>';
	}
	
	
	
	/**
	 * Outputs an HTML legend
	 *
	 * @param array $args 'attrs' array and 'title' keys.
	 *
	 * @return string HTML containing a legend.
	 */
	public function legend( $args ) {
		extract( $args );
		return sprintf(
			'<legend %s>%s</legend>', 
			$attr	= empty( $attr ) ? '' : $this->attrs_to_str( $attrs ),
			!empty( $title ) && is_string( $title ) ? $title : ucfirst( preg_replace( '|-_|', ' ', $this->group ) ) // Convert group ID
		);
	}
	
	
	/**
	 * Input Fields
	 * From here out, these functions create inputs, buttons and textareas. All functions
	 * 
	 */
	
	/*
	 * input:			The default and also the model for all inputs structure.
	 * @var str $field:	The field name.
	 * @var str $args:	A collection of additional arguments, formatted below.
	 *
	 * ARGUMENTS FORMAT
	 * $args	= array(
	 * 		$attrs		= arr, <-- HTML attributes
	 * 		$value		= str, <-- The value of the field, defaults to blank
	 * 		$type		= str, <-- The HTML Field type (text, checkbox, etc). 
	 * 						   Defaults to 'text'
	 * 		$name		= str, <-- The fully-qualified name attribute, if desired.
	 * 		$group		= str, <-- The group to which this element belongs. 
	 *		$options	= str  <-- For radio/checkbox inputs.
	 * );
	 */
	public function input( $field=null, $args=array() ) {
		if( !$field ) return;
		$type	= !empty( $args['type'] ) ? $args['type'] : 'text';
		
		// If a public method exists, then we're dealing with a form element:
		if( !empty( $type ) && is_callable( [ $this, $type ] ) ) :
			$input	= $this->{$args['type']}( $field, $args );
		// Generic text input.
		else :
			$input	= sprintf(
				'<input id="%s" type="text" name="%s" %s value="%s" />',
				$field,
				!empty( $name ) ? $name : $this->field_name( $field, !empty( $group ) ? $group : $this->group ),
				!empty( $attr ) ? $this->attrs_to_str( $attr ) : null,
				!empty( $value ) ? $value : ''
			);
		endif;
		return sprintf(
			'<div class="input %s">%s%s</div>',
			$type,
			$this->label( $field, !empty( $args['label'] ) ? $args['label'] : null ),
			$input
		);
	}
	
	public function radio( $field, $args ) {
		if( empty( $args['options'] ) ) return;
		$radios	= '';
		foreach( $args['options'] as $key=>$value ) :
			$radios	.= sprintf(
				'<input type="radio" value="%1$s" %2$s>%3$s</input>',
				$key,
				!empty( $attr ) ? $this->attrs_to_str( $attr ) : null,
				$value
			);
		endforeach;
		return $radios;
	}
	
	
	public function select( $field, $args ) {
		if( empty( $args['options'] ) ) return;
		$select		= '';
		$options	= '';
		foreach( $args['options'] as $value=>$label ) :
			$selected	= ( !empty( $args->value ) && $value == $args->value ) ? ' selected="selected" ' : '';
			$options	.= sprintf(
				'<option id="%1$s" value="%1$s"%2$s>%3$s</option>',
				$value,
				$selected,
				$label
			);
		endforeach; 
		return sprintf(
			'<select id="%1$s" %2$sname="%3$s">%4$s</select>',
			$field,
			!empty( $attr ) ? $this->attrs_to_str( $attr ) : null,
			!empty( $name ) ? $name : $this->field_name( $field, !empty( $this->group ) ? $this->group : null ),
			$options
		);
	}
	
	public function checkbox( $field, $args ) {
		if( empty( $args['options'] ) ) return;
		$boxes	= '';
		foreach( $args['options'] as $key=>$value ) :
			$boxes	.= sprintf(
				'<input name="%4$s" type="checkbox" value="%1$s" %2$s>%3$s</input>',
				$key,
				!empty( $attr ) ? $this->attrs_to_str( $attr ) : null,
				$value,
				!empty( $name ) ? $name : $this->field_name( $field, !empty( $this->group ) ? $this->group : null )
			);
		endforeach;
		return $boxes;
	}
	
	public function textarea( $field, $args ) {
		return sprintf(
			'<textarea name="%s" %s>%s</textarea>',
			$this->field_name( $field, !empty( $this->group ) ? $this->group : null ),
			!empty( $attr ) ? $this->attrs_to_str( $attr ) : null,
			!empty( $args->value ) ? $args->value : null
		);
	}
	
	
	/*
	 * label:			Create an HTML label
	 * @var str $for:	The ID of the input this label is for.
	 * @var str $text:	Optional. Label text. The ID will be used if this value is left empty.
	 * $var arr $attrs:	HTML attributes. 
	 */
	public function label( $for=null, $text=null, $attrs=null ) {
		// Bounce bad requests.
		if( empty( $for ) ) return;
		
		return sprintf(
			'<label %s %s>%s</label>', 
			!empty( $for ) ? sprintf( 'for="%s"', $for ) : '', 
			is_array( $attrs ) ? $this->attrs_to_str( $attrs ) : '', 
			!empty( $text ) && is_string( $text ) ? $text : ucfirst( preg_replace( '/[_\-]/', ' ', $for ) ) // Convert fieldname
		);
	}
	
	/*
	 * button:			Create an HTML button
	 * @var str $type:	The HTML button type.
	 * @var str $val:	The value on the button.
	 * @var arr $attrs:	HTML attributes. 
	 */
	public function button( $type='submit', $val="Submit", $attrs=null ) {
		return sprintf(
			'<button id="%1$s" type="%2$s" name="%3$s" %4$s value="%5$s">%5$s</button>',
			!empty( $name ) ? $name : '',
			!empty( $type ) ? $type : 'submit',
			!empty( $name ) ? $name : $this->field_name( $type, $this->group ),
			!empty( $attrs ) ? $this->attrs_to_str( $attrs ) : '',
			!empty( $val ) ? $val : ''
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
	public function field_name( $field=nulll ) {
		if( !$field ) return;
		return sprintf( '%s%s[%s]', $this->name, $this->group, $field);
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
	
	
	/**
	 *
	 * Our method for including and registering our classes.
	 *
	 * @param string $class The class we are including.
	 * @param object $obj The Object into which we will include our new class.
	 *
	 */
	public function registerClass( $class=null, $args=null, $obj=null ) {
		if( empty( $class ) || empty( $obj ) ) return false;
		$name	= ucwords( $class );
		$path	= plugin_dir_path(__FILE__) . sprintf( 'lib/%s.class.php', $class );
		if( file_exists( $path ) ) :
			include_once( $path );
		endif;
		$obj->{$name}	= new $name( $args );
		return $obj;
	}
	
	
	
	
	
	
	/**
	 * Giddyup.
	 *
	 * Sets defaults.
	 * 
	 * @param array|null $name Required name for the instance.
	 * @param array|null $args Optional arguments.
	 *
	 * @return void
	 */
	public function __construct( $name='EasyInputs', $args=null ) {
		$this->registerClass( 'form', $args, $this );
		die( '<pre>'. print_r( $this, true ) . '</pre>' );
	}
}
