<?php
/**
 * The Input Class of EasyInputs
 *
 * @package  EasyInputs
 * @author   Thomas J Belknap <tbelknap@holisticnetworking.net>
 * @license  GPLv2 or later
 * @link     http://holisticnetworking.net/easy-inputs-wordpress/
 */
 
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
    public $name;
    public $type;
    public $value;
    public $attrs;
    public $options;
    public $nonce_base;
    public $group;
    public $validate;
    
    
    /*
     * This function creates the HTML for the required input element.
     */
    public function create()
    {
        // If a public method exists, then we're dealing with a form element:
        if (is_callable(__NAMESPACE__ . '\Input::' . $this->type)) :
            $input  = $this->{$this->type}();
            // Generic text input.
        else :
            $input  = sprintf(
                '<input id="%s" type="text" name="%s" %s value="%s" />',
                $this->name,
                $this->fieldName( $this->name ),
                EasyInputs::attrsToString($this->attrs),
                $this->value
            );
        endif;
        // If label is set to false, do not create a label. Otherwise, use the tag or convert the field name.
        if (isset($this->args['label'])) :
            $label  = !empty($args['label']) ? Form::label($field, $args['label']) : null;
        else :
            $label  = Form::label($this->name, null);
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
        if (empty($this->options)) {
            return;
        }
        $radios = '';
        foreach ($this->options as $key => $value) :
            $radios .= sprintf(
                '<input type="radio" value="%1$s" %2$s>%3$s</input>',
                $key,
                EasyInputs::attrsToString($this->attrs),
                $value
            );
        endforeach;
        return $radios;
    }
    
    
    public function select()
    {
        if (empty($this->options)) {
            return;
        }
        $select     = '';
        $options    = '';
        foreach ($this->options as $value => $label) :
            $selected   = ( !empty($this->value) && $value == $this->value ) ? ' selected="selected" ' : '';
            $options    .= sprintf(
                '<option id="%1$s" value="%1$s"%2$s>%3$s</option>',
                $this->value,
                $selected,
                $label
            );
        endforeach;
        return sprintf(
            '<select id="%1$s" %2$sname="%3$s">%4$s</select>',
            $this->name,
            EasyInputs::attrsToString($this->attrs),
            $this->fieldName($this->name, !empty($this->group) ? $this->group : null),
            $options
        );
    }
    
    public function checkbox()
    {
        if (empty($this->options)) {
            return;
        }
        $boxes  = '';
        foreach ($this->options as $key => $value) :
            $fieldname  = !empty($this->group) ? $this->group : $this->name;
            $boxes  .= sprintf(
                '<input name="%3$s" type="checkbox" value="%1$s">%2$s</input>',
                $key,
                $value,
                $this->fieldName($fieldname)
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
            $this->fieldName($this->name, !empty($this->group) ? $this->group : null),
            EasyInputs::attrsToString($this->attrs),
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
            $this->fieldName($this->name, $this->group),
            EasyInputs::attrsToString($this->attrs),
            $this->value
        );
    }
    
    /*
     * Wrapper for wp_editor
     * 
     * @return string An HTML button tag.
     */
    public function editor() {
        return wp_editor( $this->value, $this->name, $options );
    }
    
    /*
     * Assigns a valid field name for the given input args
     * @param str $field The field-specific name.
     *
     * @return string an HTML string containing the closing fieldset tag.
     */
    public function fieldName($field = null)
    {
        if (!$field) {
            return;
        }
        $group  = implode( '', array_walk( 
            $this->group,
            function( &$value, &$key ) {
                return sprintf( '[%s]', $value );
            }
        ) );
        
        return sprintf(
            '%s%s[%s]',
            $this->Form->name,
            $group,
            $field
        );
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
    public function __construct(string $name = null, array $args = [], Form &$form = null)
    {
        if (empty($name)) {
            return;
        }
        $this->Form         = $form;
        $this->name         = $name;
        $this->attrs        = !empty($args['attrs']) ? $args['attrs'] : array();
        $this->options      = !empty($args['options']) ? $args['options'] : array();
        $this->type         = !empty($args['type']) ? $args['type'] : 'text';
        $this->value        = !empty($args['value']) ? $args['value'] : null;
        $this->group        = !empty($args['group']) ? $args['group'] : $this->Form->group;
        $this->validate     = !empty($args['validate']) ? $args['validate'] : null;
    }
}
