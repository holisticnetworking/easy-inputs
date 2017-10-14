<?php
/**
 * Test Plugin for EasyInputs
 *
 * @package EasyInputs
 * @author  Thomas J Belknap <tbelknap@holisticnetworking.net>
 * @license GPLv2 or later
 * @link    http://holisticnetworking.net/easy-inputs-wordpress/
 */

/*  Copyright 2013  Thomas J Belknap  (email : tbelknap@holisticnetworking.net)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
namespace EasyInputs;

/**
 * An example class that utilizes the EasyInputs framework.
 */

class EasyInputsExample
{
    /**
     * Register an instance of Easy Inputs
     */
    public function registerEi()
    {
        require_once plugin_dir_path(__FILE__) . '../easy-inputs/easy-inputs.php';
        // You could either declare your object a global or include it into your
        // plugin/theme's classes as necessary. Here, we declare a global:
        global $ei;

        // Instantiate EasyInputs, providing the two required settings:
        $ei = new EasyInputs([
            'name'  => 'easy-inputs-example',
            'type'  => 'setting'
        ]);

        $this->doInputs();
    }

    /**
     * Registers a set of inputs for use in your forms.
     *
     * Here we use a function to declare a list of inputs that will be used later.
     */
    public function doInputs()
    {
        global $ei;
        $ei->Form->registerInputs([
            'apple'     => [],
            'orange'    => ['type' => 'button', 'value' => 'Press an Orange!'],
            'grape'     => ['type' => 'select', 'options' => [
                'I love grapes!',
                'Bah. Grapes.'
            ]]
        ]);
    }


    /**
     * Add an options page to demonstrate the plugin.
     */
    public function addPage()
    {
        add_options_page(
            'Easy Inputs Examples',
            'Easy Inputs Examples',
            'publish_posts',
            'easy-inputs',
            [ $this, 'optionPage' ]
        );
        // Register our settings.
        add_action('admin_init', [$this,'registerSettings']);
    }
    
    
    /**
     * Register our settings.
     */
    public function registerSettings()
    {
        // Basic usage example:
        add_settings_section(
            'basic_settings_usage',
            __('Basic usage'),
            [$this, 'basicUsage'],
            'easy-inputs'
        );
        add_settings_field(
            'basic_text',
            _('A basic text field'),
            [$this, 'basicTextField'],
            'easy-inputs',
            'basic_settings_usage'
        );
        add_settings_field(
            'basic_radio',
            _('A radio button group'),
            [$this, 'basicRadio'],
            'easy-inputs',
            'basic_settings_usage'
        );
        add_settings_field(
            'basic_no_label',
            _("Let's dispense with the label."),
            [$this, 'basicNoLabel'],
            'easy-inputs',
            'basic_settings_usage'
        );

        // HTML5 inputs:
        add_settings_section(
            'html5_settings_usage',
            __('HTML5 Inputs'),
            [$this, 'html5Usage'],
            'easy-inputs'
        );
        add_settings_field(
            'html5_email',
            _('An email field'),
            [$this, 'html5Email'],
            'easy-inputs',
            'html5_settings_usage'
        );
        add_settings_field(
            'html5_datetime_local',
            _('A date time field'),
            [$this, 'html5DateTimeLocal'],
            'easy-inputs',
            'html5_settings_usage'
        );
        add_settings_field(
            'html5_color_picker',
            _('A color picker!'),
            [$this, 'html5ColorPicker'],
            'easy-inputs',
            'html5_settings_usage'
        );


        // WordPress inputs:
        add_settings_section(
            'wordpress_usage',
            __('WordPress-specific Inputs'),
            [$this, 'wordpressUsage'],
            'easy-inputs'
        );
        add_settings_field(
            'wordpress_editor',
            _('The Editor'),
            [$this, 'wordpressEditor'],
            'easy-inputs',
            'wordpress_usage'
        );
        add_settings_field(
            'wordpress_media_uploader',
            _('Yes! A Media Uploader!'),
            [$this, 'wordpressUploader'],
            'easy-inputs',
            'wordpress_usage'
        );
    }

    /**
     * Basic usage section
     */
    public function basicUsage()
    {
        echo sprintf('<p>%s</p>', _('Quick and simple setup'));
    }

    /**
     * HTML5 usage section
     */
    public function html5Usage()
    {
        echo sprintf('<p>%s</p>', _('This API also incorporates all HTML5 inputs'));
    }

    /**
     * WordPress usage section
     */
    public function wordpressUsage()
    {
        echo sprintf('<p>%s</p>', _('Yes! You can also do WordPress-specific inputs with Easy Inputs!'));
    }

    /**
     * Simple text input
     */
    public function basicTextField()
    {
        global $ei;
        echo $ei->Form->input('basic_text');
    }
    /**
     * Radio button
     */
    public function basicRadio()
    {
        global $ei;
        echo $ei->Form->input(
            'basic_radio',
            [
                'type'  => 'radio',
                'options'   => [
                    'yes'   => _('Yes'),
                    'no'    => _('No')
                ]
            ]
        );
    }
    /**
     * Don't use a label
     */
    public function basicNoLabel()
    {
        global $ei;
        echo $ei->Form->input('basic_no_label', ['label' => false]);
    }

    /**
     * HTML5 email input
     */
    public function html5Email()
    {
        global $ei;
        echo $ei->Form->input('html5_email', ['type' => 'email', 'label' => false]);
        echo sprintf('<p>%s</p>', _('Submit the form to see the validation in action'));
    }

    /**
     * HTML5 Datetime picker with local settings
     */
    public function html5DatetimeLocal()
    {
        global $ei;
        echo $ei->Form->input('html5_datetime', ['type' => 'datetime-local', 'label' => false]);
        echo sprintf('<p>%s</p>', _('The extra formatting you see for this field is generated by the browser, based on local date/time format.'));
    }

    /**
     * HTML5 color picker
     */
    public function html5ColorPicker()
    {
        global $ei;
        echo $ei->Form->input('html5_color', ['type' => 'color', 'label' => false]);
        echo sprintf('<p>%s</p>', _('Again, a browser-based color picker that converts to RGB color values. No javascript, just awesome.'));
    }

    /**
     * WordPress Editor
     */
    public function wordpressEditor()
    {
        global $ei;
        echo $ei->Form->input('wordpress_editor', ['type' => 'editor', 'label' => false]);
    }

    /**
     * WordPress Media Uploader
     */
    public function wordpressUploader()
    {
        global $ei;
        echo $ei->Form->input('wordpress_uploader', ['type' => 'uploader', 'label' => false]);
    }



    /**
     * The actual options page.
     */
    public function optionPage()
    {
        global $ei;
    
        echo '<div class="wrap"><h1>Easy Inputs and the Settings API: a Demonstration</h1>';
            echo '<p>Below you will see the output from the sample plugin\'s '
            . 'inputs. Go to the plugin file to see the function calls.</p>';
            echo '<p>These examples demonstrate how Easy Inputs can be integrated into the'
            . 'Settings API. But it will also work with metadata for posts or users. Or really,'
            . 'any application you can think of, including front-end forms.</p>';
            echo $ei->Form->open();
                settings_fields('easy-inputs');
                do_settings_sections('easy-inputs');
                echo $ei->Form->submitButton();
            echo $ei->Form->close();
        echo '</div>';
    }

    function enqueue_uploader() {
        wp_enqueue_media();
        wp_enqueue_script('uploader', plugins_url('easy-inputs/inc/js/uploader.js'));
    }
    
    /**
     * And away we go.
     */
    public function __construct()
    {
        add_action('admin_menu', [ $this, 'addPage' ]);
        add_action('admin_init', [ $this, 'registerEi' ]);
        add_action('admin_init', [ $this, 'registerSettings' ]);
        add_action('admin_enqueue_scripts', [&$this, 'enqueue_uploader']);
    }
}
