Easy Inputs
===========

A hypothetical WordPress Forms API.

Rather than aiming to simply rewrite the Settings API, this drop-in plugin is aimed at providing a generalized set of form elements with support for nonces. Because the forms are abstracted, they can be used for front end and back end forms to support themes or plugins with a consistent data format.

Installation
============

To install this drop-in, simply drop the plugin into the /wp-content/plugins folder, then include it where you want it:
```
if( !class_exists( 'EasyInputs' ) ) {
	include_once( plugins_url( 'easy-inputs.php' ) );
}
$ea	= new EasyInputs;
```

Usage
=====

Please see the included "plugin" file, testing-easy-inputs.php, for an in-place example of how to use the Easy Inputs API.

Note also that EasyInputs takes two optional arguments, the first of which specifies the name of the object once instantiated. If you would prefer not to use EasyInputs as the name, you can set it here. The name of the object will define the base POST variable like so:
```
EasyInputs[your-group-name][your-data-field]
```

An example of how to call with a custom name:
```
$mi	= new EasyInputs('MyInput');
```

Additional options can be set by the second argument, the $args array. Check the __construct() function for additional details.

