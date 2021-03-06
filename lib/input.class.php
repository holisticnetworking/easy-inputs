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
     * The HTML label for the input.
     */
    public $label;
    
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
     * Setting this value to "true" will append the input's name with [],
     * thereby making it capable of holding multiple values.
     */
    public $multiple;

    /**
     * Holds our Form instance for easy access.
     */
    public $Form;
    
    /**
     * List of supported attributes with regexes to validate against.
     */
    public $supported_attributes    = [
        'accesskey'         => '/^[a-zA-Z\-_.]{1}$/',
        'class'             => '/^[a-zA-Z\-_.]{1,200}$/',
        'tabindex'          => '/[0-9]{1,20}/',
        'width'             => '/[0-9]{1,20}/',
        'height'            => '/[0-9]{1,20}/',
        'size'              => '/[0-9]{1,20}/',
        'maxlength'         => '/[0-9]{1,20}/',
        'autocomplete'      => '/on|off/',
        'autofocus'         => '/autofocus/',
        'autosave'          => '/^[a-zA-Z\-_.]{1,200}$/',
        'results'           => '/[0-9]{1,20}/',
        'list'              => '/^[a-zA-Z\-_.]{1,200}$/',
        'min'               => '/[0-9\-\/]{1,20}/',
        'max'               => '/[0-9\-\/]{1,20}/',
        'placeholder'       => '/^[a-zA-Z\-_. \?!,:]{1,200}$/',
        'required'          => '/required/',
        'step'              => '/[0-9]{1,20}/'
    ];
    
    
    /**
     * This function creates the HTML for the required input element.
     */
    public function create()
    {
        $label  = null;
        $input  = null;
        // Do nothing unless a valid type exists:
        $function   = $this->toCamelCase($this->type);
        if (method_exists(__NAMESPACE__ . '\Input', $function)) :
            $reflector  = new ReflectionMethod(__NAMESPACE__ . '\Input::' . $function);
            // If a public method exists that matches the type property:
            if ($reflector->isPublic()) :
                $input  = $this->{$function}();
            // Generic text input.
            else :
                $input  = $this->generic();
            endif;

            // To wrap or not to wrap:
            if (in_array($this->type, ['button', 'nonce', 'nonceVerify', 'submitButton'])) :
                return $input;
            else :
                return $this->wrap($input);
            endif;
        endif;
        return __NAMESPACE__ . '\Input::' . $function;
    }

    /**
     * Return a WP Settings API nonce field.
     *
     * Port of the wp_nonce_field function. Don't over-think it.
     * Just let WordPress handle creating the nonce. This function returns,
     * rather than outputs, the nonce, in case we need to do something further
     * before output.
     *
     * @return string The output of the wp_nonce_field function.
     */
    public function nonce()
    {
        return wp_nonce_field(
            $this->Form->name,
            $this->name,
            true,
            false
        );
    }

    /**
     * Verify a nonce created by EasyInputs
     *
     * Port of the wp_verify_nonce function.
     *
     * @return boolean false, 1 or 2
     */
    public function verifyNonce()
    {
        return wp_verify_nonce($_POST[$this->name], $this->Form->name);
    }
    
    /**
     * An HTML text input
     */
    public function generic()
    {
        return sprintf(
            '<input id="%1$s" type="%2$s" name="%3$s" %4$s value="%5$s" />',
            $this->name,
            $this->type,
            $this->fieldName(),
            $this->Form->attrsToString($this->attrs),
            $this->value
        );
    }

    /**
     * A text input
     */
    public function text()
    {
        return $this->generic();
    }
    
    /**
     * A color picker input
     */
    public function color()
    {
        return $this->generic();
    }
    
    /**
     * A date picker input
     */
    public function date()
    {
        return $this->generic();
    }
    
    /**
     * A month picker input
     */
    public function month()
    {
        return $this->generic();
    }
    
    /**
     * A week picker input
     */
    public function week()
    {
        return $this->generic();
    }
    
    /**
     * A datetime picker input
     */
    public function datetime()
    {
        return $this->generic();
    }
    
    /**
     * A datetime-local picker input
     */
    public function datetimeLocal()
    {
        return $this->generic();
    }
    
    /**
     * A email input
     */
    public function email()
    {
        return $this->generic();
    }
    
    /**
     * A number input
     */
    public function number()
    {
        return $this->generic();
    }
    
    /**
     * A range slider input
     */
    public function range()
    {
        $range  = $this->generic();
        $output = $this->output();
        return $range.$output;
    }
    
    /**
     * An output for other values
     */
    public function output()
    {
        return sprintf(
            '<output id="output-%1$s" for="%1$s"></output>',
            $this->name
        );
    }
    
    /**
     * A search input
     */
    public function search()
    {
        return $this->generic();
    }
    
    /**
     * A telephone input
     */
    public function tel()
    {
        return $this->generic();
    }
    
    /**
     * A time input
     */
    public function time()
    {
        return $this->generic();
    }
    
    /**
     * A url input
     */
    public function url()
    {
        return $this->generic();
    }
    
    /**
     * An HTML radio button group
     */
    public function radio()
    {
        if (empty($this->options)) {
            return null;
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
            return null;
        }
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
            $this->fieldName(),
            $options
        );
    }
    
    /**
     * Output a group of related HTML checkboxes
     */
    public function checkbox()
    {
        if (empty($this->options)) {
            return null;
        }
        $boxes  = '';
        foreach ($this->options as $key => $data) :
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
            $this->fieldName(),
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
            $this->fieldName(),
            $this->Form->attrsToString($this->attrs),
            $this->value
        );
    }
    
    /**
     * Mimics the native WP submit_button function
     */
    public function submitButton()
    {
        return sprintf(
            '<input type="submit" name="%2$s" id="%3$s" class="%1$s" value="%4$s"  />',
            !empty($this->attrs['class']) ? $this->attrs['class'] : 'button button-primary',
            $this->name,
            !empty($this->attrs['id']) ? $this->attrs['id'] : 'submit',
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
     * Creates a media uploader-compatible input. Note that the output HTML will
     * still require the JS components of the Media Uploader to function.
     * @see https://codex.wordpress.org/Javascript_Reference/wp.media
     */
    public function uploader()
    {
        if (empty($this->value)) :
            $label  = __('Set Image');
        else :
            $image = $this->getImageId($this->value);
            $label  = wp_get_attachment_image($image, 'thumbnail');
        endif;
        $uploader   = sprintf(
            '<div class="ei-uploader hide-if-no-js">
            <a title="" href="javascript:;" class="set-image">%4$s</a><br />
            <a title="" href="javascript:;" class="remove-image">Remove Image</a>
            <input type="hidden" id="%1$s" name="%2$s" value="%3$s" />
            </div>',
            isset($this->id) ? $this->id : $this->name,
            $this->fieldName(),
            $this->value,
            $label
        );
        return $uploader;
    }

    /**
     * Return the ID of the full-size image URL given.
     * @param $image_url
     * @return mixed
     */
    public function getImageId($image_url)
    {
        global $wpdb;
        $attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", $image_url));
        return $attachment[0];
    }
    
    /**
     * Return a valid field name attribute.
     *
     * @return string The combined
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
     *
     * @return string The wrapped element.
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
     *
     * @return mixed|array An EasyInputs array of options.
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
     * Convert string to camelCase.
     *
     * @param string $string The string to convert into camelCase
     *
     * @return string aCamelCasedString, PSR2-style.
     */
    private function toCamelCase($string)
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('-', ' ', $string))));
    }

    /**
     * Construct our Object
     *
     * The $args array includes all the required values to construct an HTML element.
     *
     * @param string $name The name of the field.
     * @param array $args Either a string for the name of the field, or else an
     * array of input arguments containing the above static values.
     * @param Form $form An instance of the Easy Inputs form class.
     *
     * @return boolean True on successful creation.
     */
    public function __construct($name = null, $args = [], Form &$form = null)
    {
        if (empty($name) || empty($form)) {
            return false;
        }
        $this->Form         = $form;
        $this->name         = $name;
        $this->attrs        = !empty($args['attrs']) ? $this->Form->doAttributes($args['attrs']) : array();
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
            $this->label  = !empty($args['label']) ? $this->Form->label($this->name, $args['label']) : null;
        // Default:
        else :
            $this->label  = $this->Form->label($this->name);
        endif;
        return true;
    }
}
