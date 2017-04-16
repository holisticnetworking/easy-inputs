Easy Inputs
===========

A generic Forms generator for WordPress

This plugin introduces a template engine to WordPress for the creation of form inputs. 

Installation
============

To install this drop-in, simply drop the plugin into the /wp-content/plugins folder and activate it. 


Usage
=====

```
require_once plugin_dir_path(__FILE__) . '../easy-inputs/easy-inputs.php';
use EasyInputs\EasyInputs;

$mf	= new EasyInputs('MyForm');
```

Additional options can be set by the second argument, the $args array. Check the __construct() function for additional details.

