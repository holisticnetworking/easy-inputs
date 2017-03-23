<?php

/**
 * This class defines an HTML input.
 * 
 * The Input class of Easy Inputs is instatiated with every new input created. 
 * 
 * @property string $type The HTML Field type (text, checkbox, etc). 
 * 		Defaults to 'text'
 * @property string $value The value of the field, defaults to blank.
 * @property array $attrs HTML attributes.
 * @property array $options For radio/checkbox inputs.
 * @property string $nonce_base The base that will form our nonce fields.
 * @property string $group The group to which this element belongs. 
 */
class Input {
	private static $type, $value, $attrs, $options, $nonce_base, $group;
	
	/**
	 * Construct our Object
	 * 
	 * The $args array includes all the required values to construct an HTML element.
	 *
	 * @param array $args Includes the following keys:
	 *		
	 *
	 * @return string HTML containing a legend.
	 */
	public function __construct( $args ) {
		$this->name		= $args['name'];
		$this->type		= $args['type'];
		$this->attrs	= $args['attrs'];
	}
}