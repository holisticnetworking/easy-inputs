Easy Inputs
===========

A generic WordPress Forms Template Engine.

This plugin introduces a template engine to WordPress for the creation of form inputs. 

Installation
============

To install this drop-in, simply drop the plugin into the /wp-content/plugins folder and include it's libraries into your project. 


Usage
=====

```
require_once plugin_dir_path(__FILE__) . '../easy-inputs/easy-inputs.php';
use EasyInputs\EasyInputs;

$mf	= new EasyInputs('MyForm');
echo $mf->Form->input('MyInput');
```

Additional options can be set by the second argument, the $args array. Check the __construct() function for additional details.

Further documentation available on this project's GitHub page:
https://holisticnetworking.github.io/easy-inputs/

