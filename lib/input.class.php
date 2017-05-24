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

use ReflectionMethod;

/**
 * A class defining the HTML form element.
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
     * Can multiple values be assigned to this input? (outputs '[]')
     */
    public $multiple;
    
    
    /**
     * This function creates the HTML for the required input element.
     */
    public function create()
    {
        $label  = null;
        $input  = null;
        $reflector  = new ReflectionMethod(__NAMESPACE__ . '\Input::' . $this->type);
        // If a public method exists, then we're dealing with a form element:
        if ($reflector->isPublic()) :
            $input  = $this->{$this->type}();
        // Generic text input.
        else :
            $input  = $this->text();
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
    public function nonce()
    {
        return wp_nonce_field(
            $this->Form->name,
            $this->Form->action,
            true,
            false
        );
    }
    
    /**
     * An HTML text input
     */
    public function text()
    {
        return sprintf(
            '<input id="%s" type="text" name="%s" %s value="%s" />',
            $this->name,
            $this->fieldName(),
            $this->Form->attrsToString($this->attrs),
            $this->value
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
        foreach ($this->options as $key => $data) :
            $radios .= sprintf(
                '<label class="radios" for="%4$s-%1$s">
                    <input name="%4$s" id="%4$s-%1$s" type="radio" value="%1$s" %5$s%2$s />%3$s</label>',
                $data['value'],
                $this->Form->attrsToString($this->attrs),
                $data['name'],
                $this->fieldName(),
                $this->value == $data['value'] ? 'checked' : null
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
        if (!empty($this->options)) :
            foreach ($this->options as $value => $data) :
                $selected   = ( !empty($this->value) && $value == $this->value ) ? ' selected ' : '';
                $options    .= sprintf(
                    '<option id="%1$s" value="%1$s"%2$s>%3$s</option>',
                    $data['value'],
                    $selected,
                    $data['name']
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
        foreach ($this->options as $key => $data) :
            $fieldname  = !empty($this->group) ? $this->group : $this->name;
            $selected   = in_array($data['value'], (array)$this->value) ? 'checked' : null;
            $input  = sprintf(
                '<input id="%1$s" name="%3$s" type="checkbox" value="%1$s" %4$s><label for="%1$s">%2$s</label>',
                $data['value'],
                $data['name'],
                $this->fieldName() . '[]',
                $selected
            );
            $boxes  .= sprintf('<div class="checkboxes">%s</div>', $input);
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
     * Wrapper function to return a wp_editor instance
     */
    public function editor()
    {
        return wp_editor($this->value, $this->name);
    }
    
    /**
     * Return a valid field name attribute.
     */
    private function fieldName()
    {
        $prefix = !empty($this->Form->prefix) ? $this->Form->name : '';
        $name   = !empty($this->Form->group) || !empty($this->Form->prefix)
            ? sprintf('[%s]', $this->name)
            : $this->name;
        $group  = implode(
            '',
            array_map(
                function (&$value) {
                    return sprintf('[%s]', $value);
                },
                $this->group
            )
        );
        $multiple = !empty($this->multiple) ? '[]' : '';
        return sprintf(
            '%s%s%s%s',
            $prefix,
            $group,
            $name,
            $multiple
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
     * @param boolean $label Indicates whether or not to include a <label> element.
     */
    private function wrap($input, $label = true)
    {
        if ($label) {
            $input  = !empty($this->label) ? $this->label . $input : $input;
        }
        if ($this->wrapper) :
            return sprintf(
                $this->wrapper,
                $input
            );
        endif;
        return $input;
    }
    
    /**
     * Order our options into a consistent format
     *
     * @param array $options The passed options.
     */
    private function doOptions($options)
    {
        if (!is_array($options)) {
            return false;
        }
        foreach ($options as $key => $option) :
            if (!is_array($option)) :
                $option = [
                    'name'  => $option,
                    'value' => $key
                ];
                $options[$key]  = $option;
            endif;
        endforeach;
        return $options;
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
        $this->options      = !empty($args['options']) ? $this->doOptions($args['options']) : array();
        $this->type         = !empty($args['type']) ? $args['type'] : 'text';
        $this->value        = !empty($args['value']) ? $args['value'] : null;
        $this->label        = !empty($args['label']) ? $this->Form->label($name, $args['label']) : null;
        $this->multiple     = !empty($args['multiple']) ? true : null;
        $this->group        = !empty($args['group'])
            ? $this->Form->splitGroup($args['group'])
            : (array)$this->Form->group;
        $this->validate     = !empty($args['validate']) ? $args['validate'] : null;
        
        // Either no wrapper or a user-defined one:
        if (isset($args['wrapper'])) :
            $this->wrapper  = !empty($args['wrapper']) ? $args['wrapper'] : null;
        // Default:
        else :
            $this->wrapper  = sprintf('<div class="input %s">', $this->type) . '%s</div>';
        endif;
        
        // Either no label or a user-defined one:
        if (isset($args['label'])) :
            $this->label  = !empty($args['label']) ? $this->Form->label($name, $args['label']) : null;
        // Default:
        else :
            $this->label  = $this->Form->label($name);
        endif;
    }
}
