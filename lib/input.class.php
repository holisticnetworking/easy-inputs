<?php
namespace EasyInputs\Form;

/**
 * This class defines an HTML input.
 * 
 * The Input class of Easy Inputs is instatiated with every new input created. 
 * 
 * @param string $name The name of the input.
 * @param string $type The HTML Field type (text, checkbox, etc). 
 * 		Defaults to 'text'
 * @param string $value The value of the field, defaults to blank.
 * @param array $attrs HTML attributes.
 * @param array $options For radio/checkbox inputs.
 * @param string $nonce_base The base that will form our nonce fields.
 * @param string $group The group to which this element belongs. 
 * @param string $validate Callable validation function.
 */
class Input {
	public $name, $type, $value, $attrs, $options, $nonce_base, $group, $validate;
	
	
	/*
	 * The default function to create an Input.
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
	public function create( $field=null, $args=array() ) {
		if( !$field ) return;
		$type	= !empty( $args['type'] ) ? $args['type'] : 'text';
		
		// If a public method exists, then we're dealing with a form element:
		if( !empty( $type ) && is_callable( __NAMESPACE__ . 'Input' ) ) :
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
			Form::label( $field, !empty( $args['label'] ) ? $args['label'] : null ),
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
	 * field_name:			Assigns a valid field name for the given input args
	 * @var str $field:		The field-specific name.
	 */
	public function field_name( $field=nulll ) {
		if( !$field ) return;
		return sprintf( '%s%s[%s]', $this->name, $this->group, $field);
	}
	
	
	
	/**
	 * Construct our Object
	 * 
	 * The $args array includes all the required values to construct an HTML element.
	 *
	 * @param string|array $args Either a string for the name of the field, or else an
	 * 		array of input arguments containing the above static values.
	 *
	 * @return string HTML containing a legend.
	 */
	public function __construct( $args, &$form ) {
		if( is_array( $args ) ) : 
			$this->name			= !empty( $args['name'] ) ? $args['name'] : 'text';
			$this->type			= !empty( $args['type'] ) ? $args['type'] : 'text';
			$this->attrs		= !empty( $args['attrs'] ) ? $args['attrs'] : [];
			$this->value		= !empty( $args['value'] ) ? $args['value'] : null;
			$this->options		= !empty( $args['options'] ) ? $args['options'] : null;
			$this->group		= !empty( $args['group'] ) ? $args['group'] : null;
			$this->validate		= !empty( $args['validate'] ) ? $args['validate'] : null;
		else :
			$this->name			= $args;
			$this->type			= 'text';
			$this->nonce_base	= $form->name;
			$this->attrs		= [];
			$this->value		= null;
			$this->options		= null;
			$this->group		= null;
			$this->validate		= null;
		endif;
	}
}