<?php
namespace EasyInputs;

/**
 * This class defines an HTML input.
 * 
 * The Input class of Easy Inputs is instatiated with every new input created. 
 * 
 * @param string $name The name of the input.
 * @param string $type The HTML Field type (text, checkbox, etc). 
 *      Defaults to 'text'
 * @param string $value The value of the field, defaults to blank.
 * @param array $attrs HTML attributes.
 * @param array $options For radio/checkbox inputs.
 * @param string $nonce_base The base that will form our nonce fields.
 * @param string $group The group to which this element belongs. 
 * @param string $validate Callable validation function.
 */
class Input 
{
    public $name, $type, $value, $attrs, $options, $nonce_base, $group, $validate;
    
    
    /*
     * This function creates the HTML for the required input element.
     */
    public function create() 
    {
        // If a public method exists, then we're dealing with a form element:
        if( is_callable( __NAMESPACE__ . '\Input::' . $this->type ) ) :
            $input  = $this->{$this->type}();
        // Generic text input.
        else :
            $input  = sprintf(
                '<input id="%s" type="text" name="%s" %s value="%s" />',
                $this->name,
                $this->field_name(),
                EasyInputs::attrs_to_str( $this->attrs ),
                $this->value
            );
        endif;
        // If label is set to false, do not create a label. Otherwise, use the tag or convert the field name.
        if( isset( $this->args['label'] ) ) :
            $label  = !empty( $args['label'] ) ? Form::label( $field, $args['label'] ) : null;
        else :
            $label  = Form::label( $this->name, null );
        endif;
        return sprintf(
            '<div class="input %s">%s%s</div>',
            $this->type,
            $label,
            $input
        );
    }
    
    public function radio() 
    {
        if( empty( $this->options ) ) return;
        $radios = '';
        foreach( $this->options as $key=>$value ) :
            $radios .= sprintf(
                '<input type="radio" value="%1$s" %2$s>%3$s</input>',
                $key,
                EasyInputs::attrs_to_str( $this->attr ),
                $this->value
            );
        endforeach;
        return $radios;
    }
    
    
    public function select() 
    {
        if( empty( $this->options ) ) return;
        $select     = '';
        $options    = '';
        foreach( $this->options as $value=>$label ) :
            $selected   = ( !empty( $this->value ) && $value == $this->value ) ? ' selected="selected" ' : '';
            $options    .= sprintf(
                '<option id="%1$s" value="%1$s"%2$s>%3$s</option>',
                $value,
                $selected,
                $label
            );
        endforeach; 
        return sprintf(
            '<select id="%1$s" %2$sname="%3$s">%4$s</select>',
            $field,
            EasyInputs::attrs_to_str( $this->attr ),
            $this->field_name( $this->name, !empty( $this->group ) ? $this->group : null ),
            $options
        );
    }
    
    public function checkbox() 
    {
        if( empty( $this->options ) ) return;
        $boxes  = '';
        foreach( $this->options as $key=>$value ) :
            $boxes  .= sprintf(
                '<input name="%4$s" type="checkbox" value="%1$s">%3$s</input>',
                $key,
                $value,
                $this->field_name( $this->name, !empty( $this->group ) ? $this->group : null )
            );
        endforeach;
        return $boxes;
    }
    
    /*
     * Create an HTML textarea element.
     * @param str $field The name of the output element.
     * @param str $val The value on the button.
     * @param arr $attrs HTML attributes. 
     * 
     * @return string An HTML button tag.
     */
    public function textarea() 
    {
        return sprintf(
            '<textarea name="%s" %s>%s</textarea>',
            $this->field_name( $this->name, !empty( $this->group ) ? $this->group : null ),
            EasyInputs::attrs_to_str( $this->attr ),
            $this->value
        );
    }
    
    /*
     * Create an HTML button
     * @param str $type The HTML button type.
     * @param str $val The value on the button.
     * @param arr $attrs HTML attributes. 
     * 
     * @return string An HTML button tag.
     */
    public function button() 
    {
        return sprintf(
            '<button id="%1$s" type="%2$s" name="%3$s" %4$s value="%5$s">%5$s</button>',
            $this->name,
            $this->type,
            $this->field_name( $this->name, $this->group ),
            EasyInputs::attrs_to_str( $this->attrs ),
            $this->val
        );
    }
    
    /*
     * Assigns a valid field name for the given input args
     * @param str $field The field-specific name.
     *
     * @return string an HTML string containing the closing fieldset tag.
     */
    public function field_name( $field=null ) 
    {
        if( !$field ) return;
        return sprintf( '%s%s[%s]', $this->name, $this->group, $field);
    }
    
    
    
    /**
     * Construct our Object
     * 
     * The $args array includes all the required values to construct an HTML element.
     *
     * @param string|array $args Either a string for the name of the field, or else an
     *      array of input arguments containing the above static values.
     *
     * @return string HTML containing a legend.
     */
    public function __construct( string $name=null, array $attrs=[], array $options=[], Form &$form ) 
    {
        if( empty( $name ) ) return;
        $this->name         = $name;
        $this->attrs        = $attrs;
        $this->options      = $options;
        $this->type         = !empty( $args['type'] ) ? $args['type'] : 'text';
        $this->value        = !empty( $args['value'] ) ? $args['value'] : null;
        $this->group        = !empty( $args['group'] ) ? $args['group'] : null;
        $this->validate     = !empty( $args['validate'] ) ? $args['validate'] : null;
    }
}