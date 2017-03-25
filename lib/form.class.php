<?php
namespace EasyInputs;
use EasyInputs\Form\Input;

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
	 * @param string $name The name of the Easy Inputs instance.
	 * @param string $type Post meta, setting, etc.
	 * @param string $action The action to send the form data to.
	 * @param string $method GET, POST, etc.
	 * @param string $attrs HTML attributes.
	 * @param string $group For data saved as an array, the group name.
	 */
	public $name, $type, $action, $method, $attrs, $group, $nonce_base;
	
	
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
			'<form id="%s" action="%s" method="%s">',
			!empty( $id ) ? $id : $this->name,
			$this->action,
			$this->method
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
	 * Outputs an HTML legend
	 *
	 * @param array $args Contains 'title' and optional 'attrs' keys. 'attrs' includes 
	 * 		HTML attributes for the legend.
	 *
	 * @return string HTML containing a legend.
	 */
	public function legend( $args=[] ) {
		$title	= '';
		$attrs	= [];
		if( is_array( $args ) ) :
			extract( $args );
			$title	= !empty( $title ) ? $title : null;
			$attrs	= !empty( $attrs ) ? $attrs : [];
		else :
			$title	= $args;
		endif;
		return sprintf( '<legend %s>%s</legend>', $attrs, $title );
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
	
	
	/**
	 * Create an input.
	 * This function creates an instance of Input, supplying it all the required arguments.
	 * Input will return 
	 *
	 * @return null
	 */
	public function input( $name, $attrs=[], $options=[] ) {
		$input	= new Input( $name, $this );
		return $input->create( $name, $attrs );
	}
	
	
	/**
	 * Based on the passed type property, set the action and method values of our ojbect.
	 *
	 * @return null
	 */
	public function setType( $type ) {
		$this->action	= 'options.php';
		$this->method	= 'POST';
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
		if( empty( $args ) || empty( $args['name'] ) ) return;
		$this->name			= $args['name'];
		$this->type			= !empty( $args['type'] ) ? $args['type'] : 'post_meta';
		$this->nonce_base	= !empty( $args['nonce_base'] ) ? $args['nonce_base'] : $this->name;
		$this->attrs		= !empty( $args['attrs'] ) ? $args['attrs'] : [];
		$this->setType( $this->type );
	}
}