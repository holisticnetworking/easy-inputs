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

class TestingEasyInputs
{
    /**
     * Register an instance of Easy Inputs
     */
    public function registerEi()
    {
        require_once plugin_dir_path(__FILE__) . '../easy-inputs/easy-inputs.php';
        // Spare yourself the trouble of declaring twice:
        global $ei;
        $ei = new EasyInputs([
            'name'  => 'testing-easy-inputs',
            'type'  => 'setting'
        ]);
    }


    /**
     * Add an options page to demonstrate the plugin.
     */
    public function addPage()
    {
        add_options_page(
            'Testing Easy Inputs',
            'Easy Inputs',
            'publish_posts',
            'easy-inputs',
            [ $this, 'optionPage' ]
        );
        // Register our settings.
        add_action('admin_init', [$this,'registerSettings']);
    }
    
    
    /**
     * Add an options page to demonstrate the plugin.
     */
    public function registerSettings()
    {
        global $ei;
        register_setting($ei->Form->name, 'my_text_input');
        register_setting($ei->Form->name, 'another_text_input');
        register_setting($ei->Form->name, 'still_another_text_input');
        register_setting($ei->Form->name, 'separate_label');
        register_setting($ei->Form->name, 'radio_buttons');
        register_setting($ei->Form->name, 'color_select');
        register_setting($ei->Form->name, 'color_checkbox');
        register_setting($ei->Form->name, 'big_area_of_text');
        register_setting($ei->Form->name, 'TheGroup');
        register_setting($ei->Form->name, 'i-edit-content');
        register_setting($ei->Form->name, 'a-group-input');
        register_setting($ei->Form->name, 'a-radio-button');
        register_setting($ei->Form->name, 'an-input');
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
            // Create the form:
            echo $ei->Form->open();
                settings_fields($ei->Form->name);
                do_settings_sections('easy-inputs');
        
                // Dead-simple input inclusion:
                echo '<h2>Dead-simple input inclusion</h2>';
                echo $ei->Form->input('my_text_input');
            
                // Now, let's include a value and some HTML attributes:
                echo '<h2>Now, let\'s include a value and some HTML'
                . ' attributes:</h2>';
                echo '<p>Please see the README.md file for the proper'
                    . ' parameters and values for these. in general, all'
                    . ' HTML5-valid attributes'
                    . ' are available, including data attributes.</p>';
                echo $ei->Form->input(
                    'another_text_input',
                    [
                        'value' => 'Input Value',
                        'attrs' => [
                            'class' => 'custom classes',
                            'data-nana-nana' => 'boo-boo'
                         ],
                         'label' => 'Specify any label you want.',
                         'wrapper' => false
                    ]
                );
            
                // Labels Optional:
                echo '<h2>Labels are always optional</h2>';
                echo $ei->Form->input(
                    'still_another_text_input',
                    array(
                    'value' => 'Input Value',
                    'attrs' => array(
                        'class' => 'custom classes',
                     ),
                     'label' => 'You can create your own label'
                    )
                );
                // Or separable:
                echo '<p>';
                echo $ei->Form->input(
                    'separate_label',
                    array(
                    'value' => '42',
                    'attrs' => array(
                        'class' => 'custom classes',
                     ),
                     'label' => false
                    )
                );
                echo $ei->Form->label(
                    'separate_label',
                    'Or can even be created separately, if you like.'
                );
            
                // Radio buttons
                echo '<h2>Let\'s add some radio buttons and selects.</h2>';
                echo '<p>Radio buttons require the "options" element in'
                    . '$args be set with a $key=>$value array.</p>';
                echo $ei->Form->input(
                    'radio_buttons',
                    [
                        'type'      => 'radio',
                        'options'   => ['y' => 'Yes', 'n' => 'No'],
                        'value'     => 'y'
                    ]
                );
                echo $ei->Form->input(
                    'color_select',
                    [
                        'type' => 'select',
                        'options' => [
                            'gr' => 'Green',
                            'bl' => 'Blue',
                            'yl' => 'Yellow',
                            'rd' => 'Red',
                            'or' => 'Orange'
                        ],
                        'value' => 'bl'
                    ]
                );
                echo $ei->Form->input(
                    'color_checkbox',
                    [
                            'type' => 'checkbox',
                            'options' => [
                                'gr' => 'Green',
                                'bl' => 'Blue',
                                'yl' => 'Yellow',
                                'rd' => 'Red',
                                'or' => 'Orange'
                            ],
                            'value' => ['gr', 'bl', 'or']
                        ]
                );
            
                // Textarea
                echo '<h2>Now for a textarea</h2>';
                echo $ei->Form->input(
                    'big_area_of_text',
                    [
                        'type' => 'textarea',
                        'attrs' =>
                        [
                            'cols' => 40,
                            'rows' => 8
                        ]
                    ]
                );
            
                // Group setting:
                echo '<h2>Set groups for arrays of data</h2>';
                echo '<p>Need to group your data by arrays? Set the group for'
                . ' one input:</p>';
                echo $ei->Form->input(
                    'group_input',
                    array(
                    'value' => '42',
                    'attrs' => array(
                        'class' => 'custom classes',
                     ),
                     'group' => 'TheGroup'
                    )
                );
                echo '<p>Or set the group setting, for all future inputs:</p>';
                $ei->Form->setGroup('Nested,like,Russian,tea,dolls');
                echo $ei->Form->input('an_input');
                echo $ei->Form->input('another_input');
                echo $ei->Form->input('still_another_input');
            
            
                // WordPress Editor:
                echo '<h2>Whoa! Looka that! A WordPress Editor!</h2>';
                echo '<p>EasyInputs wraps the editor functionality into'
                    . ' itself for convenience!';
                echo $ei->Form->editor(
                    'i-edit-content'
                );
            
            
            
                // Slightly more complex, but still simple. This version is the
                // simplest way to include both your input AND an
                // automatically-generated nonce:
                echo '<h3>Slightly more complex, but still simple.</h3><p>This'
                    . ' version is the simplest way to include both your input'
                    . ' AND an automatically-generated nonce:</p>';
                echo $ei->Form->inputs(
                    [
                        'a-group-input'     => [
                            'type' => 'checkbox',
                            'options' => [ 'yes' => 'Yes', 'no' => 'No']
                        ],
                        'a-radio-button'    => [
                            'type' => 'radio',
                            'options' => [ 'yes' => 'Da', 'no' => 'Niet']
                        ],
                        'an-input'          => [
                            'label'     => 'Arbitrary Label',
                            'wrapper'   => false
                        ]
                    ]
                );
                
                // Demonstrating validated HTML attributes:
                $ei->Form->setGroup(null);
                echo $ei->Form->input(
                    'attributes',
                    ['attrs'    => [
                        'step' => '-5'
                    ],
                    'type'  => 'number']
                );
                
                echo $ei->Form->input(
                    'telephone',
                    ['attrs'    => [],
                    'type'  => 'tel']
                );
                
                
                echo $ei->Form->submit_button('Submit', ['label' => false, 'value' => 'Submit']);
        
            // Close the form:
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
    }
}
