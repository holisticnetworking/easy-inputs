<?php
/**
* Plugin Name: Easy Inputs
* Plugin URI: 
* Description: A hypothetical WordPress Forms API, governing the creation and output of
* HTML form elements.
* Version: 0.1b
* Author: Thomas J Belknap
* Author URI: http://belknap.biz
* License: GPLv2 or later
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

namespace EasyInputs;

// Include our other classes:
include_once( plugin_dir_path( __FILE__ ) . 'lib/form.class.php' );
include_once( plugin_dir_path( __FILE__ ) . 'lib/input.class.php' );
use EasyInputs\Form;
use EasyInputs\Form\Input;

/**
 * EasyInputs provides an error-free universal means of generating HTML form inputs.
 * EasyInputs is a developers-only plugin that provides a helper for generating form
 * inputs. It provides objects that represent both the Form and the Input, standarizing
 * how your HTML form elements are created, speeding development of plugins and themes. 
 * 
 */
class EasyInputs 
{
	/**
	 * For the Settings API, provide the required nonce fields.
	 *
	 * @param string $setting The Settings API setting to which this control belongs.
	 *
	 * @return string Nonce fields.
	 */
	public function hidden_fields( $setting ) 
	{
		if( empty( $setting ) ) return;
		$fields	= sprintf( '<input type="hidden" name="option_page" value="%s" /><input type="hidden" name="action" value="update" />', esc_attr( $setting ) );
		$fields	.= $this->nonce();
		return $fields;
    }
    
    
	/**
	 * Display a group of inputs
	 *
	 * @param string $group The name of our group.
	 *
	 * @return bool true
	 */
	public function set_group( $group ) 
	{
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
	public function fieldset_open( $args ) 
	{
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
	public function fieldset_close() 
	{
		return '</fieldset>';
	}
	
	
	
	/**
	 * Utility functions
	 */
	
	/**
	 * Convert HTML attributes
	 * @param array|null $attrs An array of HTML-compatible attribute/value pairs.
	 * 
	 */
	public function attrs_to_str( $attrs=null ) 
	{
		if( !is_array( $attrs ) ) return;
		$to_string	= array();
		foreach( $attrs as $key=>$val ) :
			$to_string[]	= sprintf( '%s="%s"', $key, htmlspecialchars( $val ) );
		endforeach;
		return implode( ' ', $to_string );
	}
	
	
	/**
	 * Giddyup.
	 *
	 * Sets defaults.
	 * 
	 * @param array|string|null $args Either 
	 *
	 * @return void
	 */
	public function __construct( $args=null ) 
	{
		$this->Form		= new Form( $args, $this );
	}
}
