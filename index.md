# The Last Form Builder You'll Ever Need
***
This plugin introduces a new Forms API that facilitates and speeds the creation of forms within WordPress. As much as a modern WordPress website relies on forms and inputs - for settings, post meta data, user meta data and even front end forms - there is no unified way of creating them in WordPress. The result is buggy, error-prone form creation with sprintf()'s and replacing values.

![alt text][screenshot]

[screenshot]: img/bad-screenshot.png "Be kind: sprintf() at a minimum."

Instead, this API borrows heavily from CakePHP's FormHelper to provide a uniform set of methods that will create form fields including WordPress editor windows.
To install this plugin, download the repository

## Usage
Using the plugin couldn't be easier. Simply include the plugin, instatiate the class and start creating inputs right away!

```
require_once plugin_dir_path(__FILE__) . '../easy-inputs/easy-inputs.php';
$ei = new EasyInputs(
    [
    'name'		=> 'testing-easy-inputs',
    'type'		=> 'setting',
    'nonce_base'	=> 'A World Become One, of Salads and Sun.',
    'group'		=> 'FormGroup,Subgroup,Evensubbergroup'
    ]
);

echo $ei->Form->input([
	'this-input',
	[
		'type'		=> 'text',
		'label'	=> 'This Input is Awesome!!'
		'attr'		=> [
    		'id' 		=> 'MyInput',
    		'class'	=> 'a series of classes'
    	],
	]
]);
```

As you can see, you can set baseline options that apply to both the hypothetical Form and all of it's inputs. In this case, we didn't need to create a &lt;form&gt; element, because we're creating inputs for a settings page. But we set our nonce base for any future nonces we need to create. We can also set a comma-separated list of nested groups in case we need to organize our data into arrays:

```
testing-easy-inputs[FormGroup][Subgroup][Evensubbergroup]
```

Creating an input can be as simple as passing the `name` of the field as follows:
```
echo $ei->Form->input('my-input');
```

## Easy Configuration
While a simple one-line bit of code can get you a basic form input, most any configuration you might want for your input can also be provided for with the `$args` argument.
### Forms
* The `type` element allows Easy Inputs to apply the correct `method` and `action` to the form.
* The `nonce_base` element sets the WordPress nonce field setting to be applied to the form.
* The `attrs` element provides key/value pairs for every HTML5-compatible attribute applicable to the `<form>` element.
* The `group` element provides the PHP array elements into which you plan to put all elements within the form.



### Inputs
* The `attrs` array element includes name/value pairs for all valid HTML5 attributes to be applied to the input
* The `type` element allows you to use all HTML5 input elements as well as WordPress specific wp_editor() forms.
* the `options` element provides a key/value pairing of options for checkbox, select and radio button elements.
* The `value` element contains the value to either be assigned to a text input or textarea element, or the index of `options` to be checked.
* The `group` element allows you to specify the group to apply to a single element.