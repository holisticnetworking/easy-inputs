<?php
/**
 * The Form Class of EasyInputs
 *
 * @package EasyInputs
 * @author  Thomas J Belknap <tbelknap@holisticnetworking.net>
 * @license GPLv2 or later
 * @link    http://holisticnetworking.net/easy-inputs-wordpress/
 */

namespace EasyInputs;

/**
 * This class defines an HTML form.
 *
 * An instance of class Form is created with every instance of EasyInputs. Whether or not
 * a form is created by EasyInputs, the Form class holds all the relevant form information
 * to which any Input class will need to refer.
 *
 * @param string $name The name of the Easy Inputs instance.
 * @param string $type Post meta, setting, etc.
 * @param string $action The action to send the form data to.
 * @param string $method GET, POST, etc.
 * @param string $attrs HTML attributes.
 * @param string|array $group For data saved as an array, the group name or array of
 *      subgroup names, in order of appearance.
 * @param string $nonce_base May or may not be useful.
 */
class Form
{
    public $name;
    public $type;
    public $action;
    public $method;
    public $attrs;
    public $group;
    public $nonce_base;


    /**
     * Open a form element
     *
     * This function will allow you to create the opening <form> tag with attributes.
     * It should be used in combination with the close() function. This form will also
     * optionally include WordPress nonce fields, created using the $id param.
     *
     * @param string $id|null The name of the form. Also serves as the HTML id tag. Optional
     *
     * @return string the opening tag for the form element.
     */
    public function open(string $id = null)
    {
        return sprintf(
            '<form id="%s" action="%s" method="%s" %s>',
            !empty($id) ? $id : $this->name,
            $this->action,
            $this->method,
            EasyInputs::attrsToString($this->attrs)
        );
    }
    /**
     * Close a form element
     *
     * @return string the closing tag for the form element.
     */
    public function close()
    {
        return '</form>';
    }
    
    
    /**
     * Creates a fieldset opening tag with optional legend
     *
     * The legend key of the $args array is identical to the legend() function.
     * The attrs array contains the same array of HTML attributes as always.
     *
     * @param array $args 'attrs' array and optional legend info
     *
     * @return string HTML containing the opening tag for a fieldset with
     * optional legend.
     */
    public function fieldsetOpen(array $args)
    {
        extract($args);
        return sprintf(
            '<fieldset %s>%s',
            empty($attr) ? '' : $this->attrs_to_str($attrs),
            empty($legend) ? '' : $this->legend($legend)
        );
    }
    /**
     * Creates a fieldset closing tag
     *
     * @return string HTML containing the closing tag for a fieldset.
     */
    public function fieldsetClose()
    {
        return '</fieldset>';
    }

    /**
     * Outputs an HTML legend
     *
     * @param array $args Contains 'title' and optional 'attrs' keys. 'attrs' includes
     *      HTML attributes for the legend.
     *
     * @return string HTML containing a legend.
     */
    public function legend(array $args = [])
    {
        $title  = '';
        $attrs  = [];
        if (is_array($args)) :
            extract($args);
            $title  = !empty($title) ? $title : null;
            $attrs  = !empty($attrs) ? $attrs : [];
        else :
            $title  = $args;
        endif;
        return sprintf('<legend %s>%s</legend>', $attrs, $title);
    }


    /*
     * Create an HTML label
     *
     * @param string $for The ID of the input this label is for.
     * @param string $text Optional. Label text. The ID will be used if this value is left empty.
     * $param array $attrs HTML attributes.
     *
     * @return string The HTML string for this label.
     */
    public static function label(string $for = null, string $text = null, array $attrs = null)
    {
        // Bounce bad requests.
        if (empty($for)) {
            return;
        }

        return sprintf(
            '<label %s %s>%s</label>',
            !empty($for) ? sprintf('for="%s"', $for) : '',
            is_array($attrs) ? EasyInputs::attrsToString($attrs) : '',
            !empty($text) && is_string($text) ? $text : ucfirst(preg_replace('/[_\-]/', ' ', $for)) // Convert fieldname
        );
    }


    /**
     * Create an input.
     * This function creates an instance of Input, supplying it all the required arguments.
     * Input will return
     *
     * @param string $name The name of the input element. Note that this is not the HTML
     *                     "name" attribute, but does get used to create it. If grouping
     *                     is requested, this argument will be rolled into the combined
     *                     HTML name of the group.
     * @param array  $args The args.
     *
     * @return string The HTML string for this input.
     */
    public function input(string $name, array $args = [])
    {
        return ( new Input($name, $args, $this) )->create();
    }


    /**
     * Display a group of inputs
     *
     * Defines a group of inputs, both logically and physically.
     * Logically, this group is associated with a single nonce to
     * which it is bound. Physically, all elements of a group will
     * be displayed together, in a fieldset, if requested.
     *
     * @param array $inputs Array of input arrays. Formatted with the name
     *                      of the input as the key and the $args as the
     *                      content.
     * @param array $args   Arguments meant to be applied to either all inputs
     *                      or to the container element.
     *
     * @return string A string of HTML including all inputs from $inputs.
     */
    public function inputs(array $inputs = [], array $args = [])
    {
        $args   = $this->setFieldsetDefaults($args);
        $output = '';
        $open   = is_array($args) && $args['fieldset'] ? $this->fieldsetOpen($args) : '';
        $close  = is_array($args) && $args['fieldset'] ? $this->fieldsetClose() : '';
        foreach ($inputs as $name => $args) :
            $output .= $this->input($name, $args);
        endforeach;
        return sprintf(
            '%1$s%2$s%3$s',
            $open,
            $output,
            $close
        );
        return $output;
    }
    
    /**
     * Set defaults for fieldsets.
     *
     * @param array $args Arguments meant to be applied to either all inputs
     *      or to the container element.
     *
     * @return string A string of HTML including all inputs from $inputs.
     */
    public function setFieldsetDefaults(array $args = [])
    {
        $defaults   = [
            'fieldset'  => true,
            'legend'    => ucfirst(preg_replace('/[_\-]/', ' ', $this->name)),
            'attrs'     => []
        ];
        return array_merge($defaults, $args);
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
     *
     * @return string the opening tag for the form element.
     */
    public function nonce(string $name = null, string $action = null)
    {
        return wp_nonce_field($this->action, $this->name, true, false);
    }


    /**
     * Based on the passed type property, set the action and method values of our ojbect.
     *
     * @param string $type The WordPress-compatible form type.
     *      post_meta, setting, custom
     *
     * @return null
     */
    public function setType(string $type = null)
    {
        switch ($type) :
            case 'setting':
                $this->action   = 'options.php';
                $this->method   = 'POST';
                break;
            case 'meta':
                $this->action   = 'post.php';
                $this->method   = 'POST';
                break;
            default:
                $this->action   = 'options.php';
                $this->method   = 'POST';
                break;
        endswitch;
    }


    /**
     * Display a group of inputs
     *
     * @param string $group The name of our group.
     *
     * @return bool true
     */
    public function setGroup(string $group)
    {
        $this->group    = $this->splitGroup($group);
        return true;
    }
    
    /**
     * Ensures a consistent format for group names.
     */
    public function splitGroup($group)
    {
        return explode(',', $group);
    }
    
    /**
     * Call the correct function if it exists.
     */
    public function __call($name, $settings)
    {
        if (method_exists('EasyInputs\Input', $name)) :
            $input_name             = $settings[0];
            $input_args             = isset($settings[1]) ? $settings[1] : array();
            $input_args['type']     = $name;
            return ( new Input($input_name, $input_args, $this) )->create();
        else :
            $message    = sprintf(
                'Sorry. Invalid function, %s, called.',
                $name
            );
            return $message;
            error_log($message);
        endif;
    }


    /**
     * Construct the Form object.
     *
     * @param array $args Includes all four static properties of our class.
     *
     * @return null
     */
    public function __construct(array $args)
    {
        // Bounce incomplete requests:
        if (empty($args) || empty($args['name'])) {
            return;
        }
        $this->name         = $args['name'];
        $this->type         = !empty($args['type']) ? $args['type'] : 'post_meta';
        $this->nonce_base   = !empty($args['nonce_base']) ? $args['nonce_base'] : $this->name;
        $this->attrs        = !empty($args['attrs']) ? $args['attrs'] : [];
        $this->group        = !empty($args['group']) ? $this->splitGroup($args['group']) : null;
        $this->setType($this->type);
    }
}
