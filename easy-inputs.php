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

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

class EasyInputs {
	// All of these variables will have their values set in the __construct function:
	var $name, $action, $method, $nonce_base, $validate;
	
	
	/*
	 * open:			Begin at the beginning
	 */
	public function open( $id=null ) {
		$id		= !empty( $id ) ? $id : $this->name;
		$action	= $this->action;
		$method = $this->method;
		return sprintf('<form id="%s" action="%s" method="%s">', $id, $action, $method);
	}
	public function close() {
		return '</form>';
	}
	
	
	/*
	 * nonce:			Don't overthink it. Just let WordPress handle creating the nonce.
	 * 					This function returns, rather than outputs, the nonce, in case we
	 * 					need to do something further before output.
	 * @var str $name:	The name we wish to call the nonce by
	 */
	public function nonce( $name=null, $action=null ) {
		if( !($action) ) $action = plugin_basename( __FILE__ );
		if( !($name) ) $name = $this->name;
		return wp_nonce_field( $action, $name, true, false );
	}
	
	
	/*
	 * group:			Defines a group of inputs, both logically and physically.
	 * 					Logically, this group is associated with a single nonce to
	 * 					which it is bound. Physically, all elements of a group will
	 * 					be displayed together, in a fieldset, if requested.
	 * @var arr $args:	Our array of arguments, see below.
	 * $args	= array(
	 * 		'fieldset'	=> array(
	 * 							'legend'	=> array(),
	 * 							'disabled' 	=> false,
	 * 							'name'		=> null
	 * 						),
	 * 		'inputs'	=> array()
	 * )
	 */
	public function group( $name=null, $args=array() ) {
		if( !$name or empty( $args ) ) return;
		extract( $args );
		if( empty( $action ) ) $action = plugin_basename( __FILE__ );
		
		// Each group gets its own nonce automatically:
		$result	= $this->nonce( $action, $name . '_nonce' );
		
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
	 * fieldset_open/close:		Creates a fieldset with optional legend
	 * @var arr $args:			'attrs' array and optional legend info
	 */
	public function fieldset_open( $args ) {
		extract( $args );
		$attr	= empty( $attr ) ? '' : $this->attrs_to_str( $attrs );
		$legend	= empty( $legend ) ? '' : $this->legend( $legend );
		return sprintf( '<fieldset %s>%s', $attr, $legend );
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
		if( empty( $title ) ) :
			return null;
		endif;
		$attr	= empty( $attr ) ? '' : $this->attrs_to_str( $attrs );
		return sprintf( '<legend %s>%s</legend>', $attr, $title );
	}
	
	
	/*
	 * THE FIELDS
	 */
	
	/*
	 * input:			The default and also the model for all inputs structure
	 * @var str $field:	The field name.
	 * @var str $args:	A collection of additional arguments, formatted below
	 * $args	= array(
	 * 		$attrs	= arr, <-- HTML attributes
	 * 		$value	= str, <-- The value of the field, defaults to blank
	 * 		$type	= str, <-- The HTML Field type (text, checkbox, etc). 
	 * 						   Defaults to 'text'
	 * 		$name	= str, <-- The fully-qualified name attribute, if desired.
	 * 		$group	= str  <-- The group to which this element belongs. 
	 * );
	 */
	public function input( $field=null, $args=array(), $group=null ) {
		if( !$field ) return;
		if( is_array( $args ) ) extract( $args );
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
	public function label( $str=null, $for=null, $attrs=null ) {
		$str	= ucwords( implode( ' ', explode( '_', $str ) ) );
		$is_for	= !empty( $for ) ? sprintf( 'for="%s"', $for ) : '';
		$attr	= is_array( $attrs ) ? $this->attrs_to_str( $attrs ) : '';
		return sprintf( '<label %s %s>%s</label>', $is_for, $attr, $str );
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
		$group	= !empty( $group ) ? sprintf( '[%s]', $group ) : '';
		return sprintf( '%s%s[%s]', $this->name, $group, $field);
	}
	
	
	/*
	 * SAVING THE DATA
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
	 * 		'action', <-- The action our form will call, if we're creating a full form
	 * 		'nonce_base', <-- The base string to form our nonce from
	 * 		'validate' <-- Function to which we pass our form data for validation 
	 * );
	 */
	// Ready, steady, go:
	public function __construct( $name='EasyInputs', $args=null ) {
		if( !empty( $args ) ) extract( $args );
		$this->name			= $name;
		$this->action		= empty( $action ) ? '' : $action;
		$this->method		= empty( $method ) ? 'post' : $method;
		$this->nonce_base	= empty( $nonce_base ) ? plugin_basename( __FILE__ ) : $nonce_base;
		$this->validate		= empty( $validate ) ? array( $this, 'validate' ) : $validate;
		// Check for Easy Inputs on save:
		add_action( 'save_post', 'EasyInputs::save' );
	}
}
