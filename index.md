# The Last Form Builder You'll Ever Need
***
This plugin introduces a new Forms API that facilitates and speeds the creation of forms within WordPress. As much as a modern WordPress website relies on forms and inputs - for settings, post meta data, user meta data and even front end forms - there is no unified way of creating them in WordPress. The result is buggy, error-prone form creation with sprintf()'s and replacing values.

![alt text][screenshot]

[screenshot]: img/bad-screenshot.png "Be kind: sprintf() at a minimum."

Instead, this API borrows heavily from CakePHP's FormHelper to provide a uniform set of methods that will create form fields including WordPress editor windows.
To install this plugin, download the repository

## Usage

```
require_once plugin_dir_path(__FILE__) . '../easy-inputs/easy-inputs.php';
$ei = new EasyInputs(
    [
    'name'		=> 'testing-easy-inputs',
    'type'		=> 'setting',
    'attr'		=> [
    	'id' 		=> 'MyForm',
    	'class'	=> 'a series of classes'
    ],
    'nonce_base'	=> 'A World Become One, of Salads and Sun.',
    'group'		=> 'FormGroup,Subgroup,Evensubbergroup'
    ]
);

echo $ei->Form->input([
	'this-input',
	[
		'type'		=> 'text',
		'label'	=> 'This Input is Awesome!!'
	]
]);
```

Additional options can be set by the second argument, the $args array. Check the __construct() function for additional details.

