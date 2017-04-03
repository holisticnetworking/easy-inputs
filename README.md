Easy Inputs
===========

A generic WordPress Forms Template Engine.

This plugin introduces a template engine to WordPress for the creation of form inputs. 

Installation
============

To install this drop-in, simply drop the plugin into the /wp-content/plugins folder and activate it. 


Usage
=====

```
if( !class_exists( 'EasyInputs' ) ) {
	include_once( plugins_url( 'easy-inputs.php' ) );
}
$ea	= new EasyInputs;
```
Please see the included "plugin" file, testing-easy-inputs.php, for an in-place example of how to use the Easy Inputs API.

Note also that EasyInputs takes two optional arguments, the first of which specifies the name of the object once instantiated. If you would prefer not to use EasyInputs as the name, you can set it here. The name of the object will define the base POST variable like so:
```
EasyInputs[your-group-name][your-data-field]
```

An example of how to call with a custom name:
```
$mi	= new EasyInputs('MyForm');
```

Additional options can be set by the second argument, the $args array. Check the __construct() function for additional details.

