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
	var $name	= 'EasyInputs';
	/*///////////////
	//  The Forms  //
	///////////////*/
	public function input( $args ) {
		extract( $args );
		$name		= isset($name) ? esc_attr($name) : 'blank';
		$label		= isset($label) ? esc_attr($name) : null;
		$value		= isset($value) ? esc_attr($name) : '';
		$size		= isset($size) ? esc_attr($size) : 20;
		$maxlength	= isset($maxlength) ? esc_attr($maxlength) : 100;
		$wrap		= isset($wrap) ? $wrap : true;
		$class		= isset($class) ? 'class="' . $class . '"' : null;
		
		$input		= '';
		if( $wrap ) : $input .= '<p>'; endif;
		if(!empty($label)) :
			echo '<label for="' . $name . '">' . $label . ':</label>';
		endif;
		echo '<input type="text" ' . $class . ' id="' . $name . '" name="easy-inputs[' . $name . ']" value="' . esc_attr($value) . '" size="' . $name . '" maxlength="' . $name . '" />';
		if( $wrap ) : $input .= '</p>'; endif;
		echo $input;
	}
	
	// One nonce for all Easy Inputs data:
	public function nonce() {
		wp_nonce_field( 'easy-inputs', $this->name );
	}
	
	
	// Check for and save Easy Inputs fields:
	public function save_inputs( $post_id ) {
		if(!empty($_POST['easy-inputs'])) :
			// Refuse without valid nonce:
			if ( ! isset( $_POST[$this->name . '_nonce'] ) || ! wp_verify_nonce( $_POST[$this->name . '_nonce'], 'easy-inputs' ) ) return;
			foreach($_POST['easy-inputs'] as $key=>$input) :
				
				
				// Sanitize input:
				$value	= EasyInputs::sanitize($input['value']);
			endforeach;
		endif;
	}
	
	private function sanitize($val) {
		if(is_array($val)) :
			foreach($val as $k=>$v) :
				$val[$k]	= EasyInputs::sanitize($v);
			endforeach;
		else :
			$val	= sanitize_text_field($val);
		endif;
		return $val;
	}
	
	// Ready, steady, go:
	public function __construct( $name='EasyInputs' ) {
		$this->name	= $name;
		// Check for Easy Inputs on save:
		add_action( 'save_post', 'EasyInputs::save_inputs' );
	}
}
