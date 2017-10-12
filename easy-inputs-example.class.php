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
    }

    /**
     * Basic usage section
     */
    public function basicUsage() {
        echo sprintf('<p>%s</p>', _('Quick and simple setup'));
    }

    /**
     * Simple text input
     */
    public function basicTextField() {
        global $ei;
        echo $ei->Form->input('basic_text');
    }
    /**
     * Radio button
     */
    public function basicRadio() {
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
    public function basicNoLabel() {
        global $ei;
        echo $ei->Form->input('basic_no_label', ['label' => false]);
    }



    /**
     * The actual options page.
     */
    public function optionPage()
    {
        global $ei;
    
        echo '<div class="wrap"><h1>Demonstrating Easy Inputs</h1>';
            echo '<p>Below you will see the output from the sample plugin\'s '
            . 'inputs. Go to the plugin file to see the function calls.</p>';
            echo $ei->Form->open();
                settings_fields('easy-inputs');
                do_settings_sections('easy-inputs');
                echo $ei->Form->submitButton();
            echo $ei->Form->close();
        echo '</div>';
    }
    
    /**
     * And away we go.
     */
    public function __construct()
    {
        add_action('admin_menu', [ $this, 'addPage' ]);
        add_action('admin_init', [ $this, 'registerEi' ]);
        add_action('admin_init', [ $this, 'registerSettings' ]);
    }
}
