<?php
/**
 * A WordPress Forms template engine.
 * 
 * @category Wordpress
 * @package  EasyInputs
 * @author   Thomas J Belknap <tbelknap@holisticnetworking.net>
 * @license  GPLv2 or later
 * @link     http://holisticnetworking.net/easy-inputs-wordpress/
 */
/*
Plugin Name: Easy Inputs
Plugin URI:
Description: A WordPress Forms template engine.
Version: 0.1b
Author: Thomas J Belknap
Author URI: http://belknap.biz
License: GPLv2 or later
*/

namespace EasyInputs;

// Include our other classes:
require_once plugin_dir_path(__FILE__) . 'lib/form.class.php';
require_once plugin_dir_path(__FILE__) . 'lib/input.class.php';
use EasyInputs\Form;
use EasyInputs\Form\Input;

/**
 * Error-free HTML form and input template engine.
 *
 * EasyInputs provides an error-free universal means of generating HTML form
 * inputs. EasyInputs is a developers-only plugin that provides a helper for
 * generating form inputs. It provides objects that represent both the Form and
 * the Input, standarizing how your HTML form elements are created, speeding
 * development of plugins and themes.
 *
 * @category Wordpress
 * @package  EasyInputs
 * @author   Thomas J Belknap <tbelknap@holisticnetworking.net>
 * @license  GPLv2 or later
 * @link     http://holisticnetworking.net/easy-inputs-wordpress/
 */
class EasyInputs
{
    /**
     * For the Settings API, provide the required nonce fields.
     *
     * @param string $setting The Settings API setting to which this control
     * belongs.
     *
     * @return string Nonce fields.
     */
    public function hiddenFields(string $setting)
    {
        if (empty($setting)) {
            return;
        }
        $fields = sprintf(
            '<input type="hidden" name="option_page" value="%s" />
            <input type="hidden" name="action" value="update" />',
            esc_attr($setting)
        );
        $fields .= $this->nonce();
        return $fields;
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
     * Convert HTML attributes
     * Passed an indexed array of attribute/value pairs, this function will
     * return them as valid HTML attributes in a string.
     *
     * @param array $attrs An array of HTML-compatible attribute/value pairs.
     *
     * @return string The attributes as a string.
     */
    public static function attrsToString(array $attrs)
    {
        if (empty($attrs)) {
            return;
        }
        $to_string  = array();
        foreach ($attrs as $key => $val) :
            $to_string[]    = sprintf('%s="%s"', $key, htmlspecialchars($val));
        endforeach;
        return implode(' ', $to_string);
    }
   
   
    /**
     * Giddyup.
     * This function constructs our EasyInputs class for use in WordPress. Each
     * time a new instance of EasyInputs is created, a new Form class is
     * created. While not all WordPress forms require actual <form> tags, the
     * Form class acts as our model for internal representation of the form two
     * which our EasyInputs class is being applied.
     *
     * @param array $args An array of arguments that instantiates the Form
     * class. Minimally, this array needs to include a 'name', and preferably
     * also a 'type' value. The name is intended to be HTML compatible and is
     * used for certain values unless overridden. The type must be either
     * post_meta, setting or custom, correlating to the types of supported form
     * elements.
     *
     * @return void
     */
    public function __construct(array $args)
    {
        // Bounce incomplete requests:
        if (empty($args) || empty($args['name'])) {
            return;
        }
        $this->Form     = new Form($args, $this);
    }
}
