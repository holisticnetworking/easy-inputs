<?php
namespace EasyInputs;

// Include our other classes:
include_once( plugin_dir_path( __FILE__ ) . 'lib/form.class.php' );
include_once( plugin_dir_path( __FILE__ ) . 'lib/input.class.php' );
use EasyInputs\Form;
use EasyInputs\Form\Input;

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
	 **/
	
	
	
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
	
	
	/**
	 *
	 * Our method for including and registering our classes.
	 *
	 * @param string $class The class we are including.
	 * @param object $obj The Object into which we will include our new class.
	 *
	
	public function registerClass( $class=null, $args=null, $obj=null ) {
		if( empty( $class ) || empty( $obj ) ) return false;
		$name	= ucwords( $class );
		$path	= plugin_dir_path(__FILE__) . sprintf( 'lib/%s.class.php', $class );
		if( file_exists( $path ) ) :
			include_once( $path );
		endif;
		$obj->{$name}	= new \EasyInputs\{$name}( $args, $obj );
		return $obj;
	}
	 */
	
	
	
	
	
	/**
	 * Giddyup.
	 *
	 * Sets defaults.
	 * 
	 * @param array|string|null $args Either 
	 *
	 * @return void
	 */
	public function __construct( $args=null ) {
		$this->form		= new Form( $args, $this );
	}
}
