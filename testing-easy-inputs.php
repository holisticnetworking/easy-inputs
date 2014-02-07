<?php
/*
Plugin Name: Testing Easy Inputs
Plugin URI: https://github.com/holisticnetworking/easy-inputs
Description: Testing and demonstrating Easy Inputs.
Version: 0.1-beta
Author: Thomas J. Belknap
Author URI: http://holisticnetworking.net
*/

/*  Copyright 2013  Thomas J Belknap  (email : tbelknap@holisticnetworking.net)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

function add_page() {
	add_options_page( 'Testing Easy Inputs', 'Easy Inputs', 'publish_posts', 'easy-inputs', 'options_page');
}

function options_page() {
	// First, instantiate the class, checking to make sure no other plugin or theme
	// has already included the file:
	if( !class_exists( 'EasyInputs' ) ) {
		include_once( plugin_dir_path( __FILE__ ) . '../easy-inputs/easy-inputs.php' );
	}
	$ei	= new EasyInputs();
	
	// Dead-simple input inclusion:
	echo '<h3>Dead-simple input inclusion</h3>';
	echo $ei->input( 'my_text_input' );
	
	
	// Now, let's include a value and some HTML attributes:
	echo '<h3>Now, let\'s include a value and some HTML attributes:</h3>';
	echo $ei->input( 'another_text_input', array(
		'value'	=> 'Input Value',
		'attrs'	=> array('class' => 'custom classes', 'data-value' => 'Nana, nana, boo-boo'),
		'label' => 'Specify any label you want, man.'
	) );
	// Labels Optional:
	echo '<p>Labels optional:</p>';
	echo $ei->input( 'another_text_input', array(
		'value'	=> 'Input Value',
		'attrs'	=> array('class' => 'custom classes', 'data-value' => 'Nana, nana, boo-boo'),
		'label' => false
	) );
	// Or even separable:
	echo '<br />';
	echo $ei->input( 'separate_label', array(
		'value'	=> '42',
		'attrs'	=> array('class' => 'custom classes', 'data-value' => 'Nana, nana, boo-boo'),
		'label' => false
	) );
	echo $ei->label( 'Or even separable, if you like', 'separate_label' );
	
	
	
	// Slightly more complex, but still simple. This version is the simplest way
	// to include both your input AND an automatically-generated nonce:
	echo '<h3>Slightly more complex, but still simple.</h3><p>This version is the simplest way to include both your input AND an automatically-generated nonce:</p>';
	echo $ei->group( 'mygroup', array( 'inputs' => array( 'my_input' ) ) );
	
	
	/* */
	echo '<h3>Considerably more complex</h3><p>We treat each input as a single call to the input() function, include a fieldset and legend.</p>';
	echo $ei->group( 'seuss-group', array( 
		'fieldset'	=> array(
			'attrs'		=> array( 'class' => 'sneetch' ),
			'legend'	=> array( 'title' => "Don't cry because it's over, smile because it happened." )
		),
		'inputs' => array( 
			'one-input'		=> array( 'attrs'	=> array( 'class' => 'my-custom-class' ) ),
			'two-input'		=> array( 'value' => 'Cindy-loo Hoo' ),
			'red-input'		=> array( 'attrs'	=> array( 'data-stars' => 'on thars' ) ),
			'blue-input'	=> array( 'label' => 'Custom Label' )
		) ) );
	/* */
}
add_action('admin_menu', 'add_page');
?>
