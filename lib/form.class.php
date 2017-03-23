<?php

/**
 * This class defines an HTML form.
 * 
 * An instance of class Form is created with every instance of EasyInputs. Whether or not
 * a form is created by EasyInputs, the Form class holds all the relevant form information
 * to which any Input class will need to refer.
 * 
 * @property string $name (required) The name of our instance, also used as the ID unless
 * 		otherwise specified by the $attrs['id'] index.
 * @property string $type (required) WP form type. Values include post_meta, setting.
 * @property string $action The HTML action attribute.
 * @property string $method 
 * @property array $attrs HTML attributes, applicable to the form itself. Used to produce
 *		 
 */
class Form {
	/**
	 * @var string $name The name of the Easy Inputs instance.
	 * @var string $type Post meta, setting, etc.
	 * @var string $action The action to send the form data to.
	 * @var string $method GET, POST, etc.
	 * @var string $validate Callable validation function.
	 * @var string $group For data saved as an array, the group name.
	 */
	private static $name, $type, $action, $method, $validate, $attrs;
	
	/**
	 * Based on the passed type property, set the action and method values of our ojbect.
	 *
	 * @return null
	 */
	public function setType() {
		
	}
	
	
	/**
	 * Construct the Form object.
	 *
	 * @param array $args Includes all four static properties of our class.
	 *
	 * @return null
	 */
	public function __construct( $args ) {
		// Bounce incomplete requests:
		if( empty( $args ) || empty( $args['name'] || empty( $args['type'] ) ) ) return;
		$this->name		= $args['name'];
		$this->type		= $args['type'];
		$this->attrs	= $args['attrs'];
		$this->setType( $this->type );
		parent::registerClass( 'input', $args, $this );
	}
}