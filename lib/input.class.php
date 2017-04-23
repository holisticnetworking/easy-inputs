<?php
/**
 * The Input Class of EasyInputs
 *
 * @package EasyInputs
 * @author  Thomas J Belknap <tbelknap@holisticnetworking.net>
 * @license GPLv2 or later
 * @link    http://holisticnetworking.net/easy-inputs-wordpress/
 */
 
namespace EasyInputs;

/**
 * This class defines an HTML input.
 *
 * The Input class of Easy Inputs is instatiated with every new input created. What is
 * returned is always a string. The create() function is used as the main entry point
 * to the Input class, calling the function specified by the $args element 'type'. As
 * a default, all Inputs are created as text inputs unless a specfic type is requested
 * in that element.
 */
class Input
{
    /**
     * The name of the instance of EasyInputs\Form\Input
     */
    public $name;
    
    /**
     * Text, textarea, radio, etc.
     */
    public $type;
    
    /**
     * A value to assign the input, or the selected element of a radio, checkbox
     * or select.
     */
    public $value;
    
    /**
     * An optional array of HTML attributes to apply to the element.
     */
    public $attrs;
    
    /**
     * Data attribute/value pairs.
     *
     * For any data-* attributes you may wish to add to the element, this array provides
     * an attribute/value pair. Attribute names need not include "data-" prefix.
     * @var array
     */
    public $data;
    
    /**
     * For elements requiring options, they are held here.
     */
    public $options;
    
    /**
     * A basis for a nonce field
     */
    public $nonce_base;
    
    /**
     * A comma-separated list of nested groups
     */
    public $group;
    
    /**
     * A validating function callback.
     */
    public $validate;
    
    /**
     * A sprintf-compatible wrapper for the input.
     */
    public $wrapper;
    
    
    /**
     * This function creates the HTML for the required input element.
     */
    public function create()
    {
        $label  = null;
        $input  = null;
        // If a public method exists, then we're dealing with a form element:
        if (is_callable(__NAMESPACE__ . '\Input::' . $this->type)) :
            $input  = $this->{$this->type}();
        // Generic text input.
        else :
            $input  = sprintf(
                '<input id="%s" type="text" name="%s" %s value="%s" />',
                $this->name,
                $this->fieldName(),
                $this->Form->attrsToString($this->attrs),
                $this->value
            );
        endif;
        return $this->wrap($input);
    }
    
    /**
     * Return a WP Settings API nonce field.
     *
     * Don't overthink it. Just let WordPress handle creating the nonce.
     * This function returns, rather than outputs, the nonce, in case we
     * need to do something further before output.
     *
     * @param string $name   A name from which to create our nonce.
     * @param string $action The action requiring our nonce.
     * @param bool $referer Whether to include a referer input as well.
     * @param bool $echo Whether to echo or return as a value.
     *
     * @return string the opening tag for the form element.
     */
    public static function nonce($name = '', $action = '', $referer = true, $echo = false)
    {
        return wp_nonce_field(
            $name,
            $action,
            $referer,
            $echo
        );
    }
    
    /**
     * An HTML radio button group
     */
    public function radio()
    {
        if (empty($this->options)) {
            return;
        }
        $radios = '';
        foreach ($this->options as $value => $label) :
            $radios .= sprintf(
                '<input name="%4$s" type="radio" value="%1$s" %2$s>%3$s</input>',
                $value,
                $this->Form->attrsToString($this->attrs),
                $label,
                $this->fieldName()
            );
        endforeach;
        return $radios;
    }
    
    /**
     * Output an HTML select box with inputs
     */
    public function select()
    {
        if (empty($this->options)) {
            return;
        }
        $select     = '';
        $options    = '';
        if(!empty($this->options)) :
            foreach ($this->options as $value => $label) :
                $selected   = ( !empty($this->value) && $value == $this->value ) ? ' selected="selected" ' : '';
                $options    .= sprintf(
                    '<option id="%1$s" value="%1$s"%2$s>%3$s</option>',
                    $this->value,
                    $selected,
                    $label
                );
            endforeach;
        endif;
        return sprintf(
            '<select id="%1$s" %2$sname="%3$s">%4$s</select>',
            $this->name,
            $this->Form->attrsToString($this->attrs),
            $this->fieldName($this->name, !empty($this->group) ? $this->group : null),
            $options
        );
    }
    
    /**
     * Output a group of related HTML checkboxes
     */
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
                $this->fieldName() . '[]'
            );
        endforeach;
        return $boxes;
    }
    
    /**
     * Return an HTML textarea element.
     */
    public function textarea()
    {
        return sprintf(
            '<textarea name="%s" %s>%s</textarea>',
            $this->fieldName($this->name, !empty($this->group) ? $this->group : null),
            $this->Form->attrsToString($this->attrs),
            $this->value
        );
    }
    
    /**
     * Return an HTML button
     */
    public function button()
    {
        return sprintf(
            '<button id="%1$s" type="%2$s" name="%3$s" %4$s value="%5$s">%5$s</button>',
            $this->name,
            $this->type,
            $this->fieldName($this->name, $this->group),
            $this->Form->attrsToString($this->attrs),
            $this->value
        );
    }
    
    /**
     * Mimics the native WP submit_button function
     */
    public function submit_button()
    {
        return sprintf(
            '<input type="submit" name="%2$s" id="%3$s" class="%1$s" value="%4$s"  />',
            !empty($this->args['class']) ? $this->args['class'] : 'button button-primary',
            $this->name,
            !empty($this->args['id']) ? $this->args['id'] : 'submit',
            !empty($this->value) ? $this->value : 'Save Changes'
        );
    }
    
    /**
     * Wrap input in the requested HTML.
     *
     * This function performs two essential tasks. The first is to include the requested
     * <label> element to the input. The second is to wrap the result in either user-requested
     * HTML or the default.
     *
     * @param string $input The unwrapped HTML input element
     */
    public function wrap($input)
    {
        $input  = !empty($this->label) ? $this->label . $input : $input;
        if($this->wrapper) :
            return sprintf(
                $this->wrapper,
                $input
            );
        endif;
        return $input;
    }
    
    /**
     * Wrapper function to return a wp_editor instance
     */
    public function editor()
    {
        return wp_editor($this->value, $this->name);
    }
    
    /**
     * Return a valid field name attribute.
     */
    public function fieldName()
    {
        $group  = implode(
            '',
            array_map(
                function (&$value) {
                    return sprintf('[%s]', $value);
                },
                $this->group
            )
        );
        return sprintf(
            '%s%s[%s]',
            $this->Form->name,
            $group,
            $this->name
        );
    }
    
    
    
    /**
     * Construct our Object
     *
     * The $args array includes all the required values to construct an HTML element.
     *
     * @param string $name The name of the field.
     * @param array $args Either a string for the name of the field, or else an
     * array of input arguments containing the above static values.
     * @param EasyInputs\Form $form An instance of the Easy Inputs form class.
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
        $this->label        = !empty($args['label']) ? $this->Form->label($name, $args['label']) : null;
        $this->group        = !empty($args['group']) ? $this->Form->splitGroup($args['group']) : $this->Form->group;
        $this->validate     = !empty($args['validate']) ? $args['validate'] : null;
        
        // Either no wrapper or a user-defined one:
        if(isset($args['wrapper'])) :
            $this->wrapper  = !empty($args['wrapper']) ? $args['wrapper'] : null;
        // Default:
        else :
            $this->wrapper  = sprintf('<div class="input %s">', $this->type) . '%s</div>';
        endif;
        
        // Either no label or a user-defined one:
        if(isset($args['label'])) :
            $this->label  = !empty($args['label']) ? $this->Form->label($name, $args['label']) : null;
        // Default:
        else :
            $this->label  = $this->Form->label($name);
        endif;
    }
}
