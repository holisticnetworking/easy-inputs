=== Easy Inputs ===
Contributors: DragonFlyEye
Tags: inputs, metadata, settings, html, forms
Requires at least: 4.0
Tested up to: 4.9.7
Requires PHP: 5.6
Stable tag: 1.2.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Error-free HTML form and input template engine for WordPress.

== Description ==
EasyInputs provides an error-free universal means of generating HTML form inputs. EasyInputs is a developers-only plugin that provides a helper for generating form inputs. It provides objects that represent both the Form and the Input, standarizing how your HTML form elements are created, speeding development of plugins and themes.


== Installation ==
1. Upload the easy-inputs directory to your site\'s /wp-content/plugins directory
1. Include the easy-inputs.php file into your project with the below code:
```
require_once plugin_dir_path(__FILE__) . \'../easy-inputs/easy-inputs.php\';
// You could either declare your object a global or include it into your
// plugin/theme\'s classes as necessary. Here, we declare a global:
global $ei;

// Instantiate EasyInputs, providing the two required settings:
$ei = new EasyInputs([
    \'name\'  => \'easy-inputs-example\',
    \'type\'  => \'setting\'
]);
```

Please see the example plugin contained in the easy-inputs directory for further example code. Detailed notes can be found on the [Github page for this plugin](https://holisticnetworking.github.io/easy-inputs/)

== Frequently Asked Questions ==
= I don\'t see any way to activate this plugin. Why is that? =
This is not a \"plugin\" in the traditional sense. It is for developers who want a toolbox with which to develop forms and inputs for WordPress administrative uses. THIS IS NOT FOR NOVICE OR NON-DEVELOPER USE!
= Is this plugin like Gravity Forms =?
NO! This plugin is designed for developers to speed the development of new WordPress projects and while it can do front-end forms, it is not a substitute for other plugins better designed for end users. DO NOT USE THIS PLUGIN UNLESS YOU UNDERSTAND PHP!

== Changelog ==
= 1.2.1 =
First release to the WordPress Plugins Directory
= 1.2.2 =
Sample code is confusing the WordPress plugin updater. Removing. 
