Easy Inputs
===========

A hypothetical WordPress Forms API, meant to replace the Settings API.

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

