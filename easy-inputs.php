<?php
/**
 * A WordPress Forms template engine.
 *
 * @package EasyInputs
 * @author  Thomas J Belknap <tbelknap@holisticnetworking.net>
 * @license GPLv2 or later
 * @link    http://holisticnetworking.net/easy-inputs-wordpress/
 */
/**
 * Plugin Name: Easy Inputs
 * Plugin URI: https://holisticnetworking.github.io/easy-inputs/
 * Description: A WordPress Forms template engine.
 * Version: 1.0
 * Author: Thomas J Belknap
 * Author URI: http://holisticnetworking.net
 * GitHub Plugin URI: holisticnetworking/easy-inputs
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

namespace EasyInputs;

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
     * Giddyup.
     * This function constructs our EasyInputs class for use in WordPress. Each
     * time a new instance of EasyInputs is created, a new Form class is
     * created. While not all WordPress forms require actual &lt;form&gt; tags,
     * the Form class acts as our model to represent the overall form. It
     * defines what kind of data we're handling, a default name to be applied to
     * the data and a few other details.
     *
     * @param array $args An array of arguments that instantiates the Form
     * class. Minimally, this array needs to include a 'name', and preferably
     * also a 'type' value. The name is intended to be HTML compatible and is
     * used for certain values unless overridden. The type must be either
     * meta, setting or custom, correlating to the types of supported form
     * elements.
     *
     * @return void
     */
    public function __construct(array $args)
    {
        // Include our other classes:
        require_once plugin_dir_path(__FILE__) . 'lib/form.class.php';
        require_once plugin_dir_path(__FILE__) . 'lib/input.class.php';
        
        // Bounce incomplete requests:
        if (empty($args) || empty($args['name'])) {
            return;
        }
        $this->Form     = new Form($args, $this);
    }
}
