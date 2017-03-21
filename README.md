Easy Inputs
===========

A generic WordPress Forms API.

This plugin does not seek to replace the Settings API, the Customizer or meta generation in WordPress. It seeks simply to create a unified Forms API that provides universal control to form elements. This acheives the end of allowing developers to create beautiful, intuitive interfaces for their plugins and themes without the hassle of generating compatible forms and inputs.

Easy Inputs is intended to be used for post metadata, Settings or Customizer forms. As it sits right now, it's basically a pile of HTML form inputs, opening and closing functions for forms and fieldsets. It also includes a function for creating nonces.

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

