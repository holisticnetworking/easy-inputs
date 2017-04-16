A generic Forms generator for WordPress

This plugin introduces a new Forms API that facilitates and speeds the creation of forms within WordPress. As much as a modern WordPress website relies on forms and inputs - for settings, post meta data, user meta data and even front end forms - there is no unified way of creating them in WordPress. The result is buggy, error-prone form creation with sprintf()'s and replacing values.

Instead, this API borrows heavily from CakePHP's FormHelper to provide a uniform set of methods that will create form fields including WordPress editor windows.

Installation
============

To install this plugin, download the repository 


Usage
=====

```
require_once plugin_dir_path(__FILE__) . '../easy-inputs/easy-inputs.php';
use EasyInputs\EasyInputs;

$mf	= new EasyInputs('MyForm');
```

Additional options can be set by the second argument, the $args array. Check the __construct() function for additional details.

