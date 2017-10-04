# Step Aside, sprintf(). Here comes something awesome-er.
***
As dependent as a modern WordPress website is on forms and inputs - for settings, post meta data, user meta data and even front end forms - there is no unified way of creating them in WordPress. The result is buggy, error-prone form creation with sprintf()'s and replacing values. Not only buggy and error prone, building HTML form elements "the old fashioned way" is also BORING and SLOW. What if there was a faster, easier way?

![alt text][screenshot]

[screenshot]: img/bad-screenshot.png "Be kind: sprintf() at a minimum."

As a solution, this API borrows heavily from CakePHP's FormHelper to provide a uniform set of methods that will create form fields including WordPress editor windows. In this way, setting up the configuration of a WordPress form or form field feels a lot more like configuring other WP elements such as `wp_nav_menu()`.

## Installation
To install this API, clone the repository into your /wp-content/plugins directory. There is no need to "activate" this "plugin". Once the files are in place, simply include them into your project as shown below.

## Usage
Using the API couldn't be easier. Simply include the API, instantiate the class and start creating inputs right away!
```
require_once plugin_dir_path(__FILE__) . '../easy-inputs/easy-inputs.php';
$ei = new EasyInputs([
    'name' => 'testing-easy-inputs',
    'type' => 'setting',
    'nonce_base' => 'A World Become One, of Salads and Sun.',
    'group' => 'FormGroup,Subgroup,Evensubbergroup'
]);
```
The only two required settings to include are the type and name of the form. `type` helps define the required elements for the form. `name` will be used as a prefix to inputs unless otherwise specified and goes into creating our nonces. Since we're creating a setting, we don't need to worry about creating the `form` element. Instead, we can move right on to building our form elements.

As you can see, you can set baseline options that apply to both the hypothetical Form and all of it's inputs. But we set our nonce base for any future nonces we need to create. We can also set a comma-separated list of nested groups in case we need to organize our data into arrays:

```
testing-easy-inputs[FormGroup][Subgroup][Evensubbergroup]
```

In the event that a given input within a form needs to be held in a different array, we can use the `group` element on the input as well. See below for further information.

Creating a text input can be as simple as passing the `name` of the field as follows:
```
echo $ei->Form->input('my-input');
```

Want a radio button group? No sweat:
```
echo $ei->Form->input('my-radio', ['type' => 'radio', 'options' => ['yes' => 'Yes', 'no' => 'No']]);
```

The above code will output:
```
<div class="input radio">
    <label for="my-radio">My Radio</label>
    <label class="radios" for="MyForm[my-radio]-yes"><input name="MyForm[my-radio]" id="MyForm[my-radio]-yes" type="radio" value="yes">Yes</label>
    <label class="radios" for="MyForm[my-radio]-no"><input name="MyForm[my-radio]" id="MyForm[my-radio]-no" type="radio" value="no">No</label>
</div>
```
Don't want that pesky `div` wrapping your input? No sweat:
```echo $ei->Form->input('my-radio', ['type' => 'radio', 'options' => ['Yes', 'No'], 'div' => false]);```

Here's a more involved case. In this instance, we're also adding a custom label and setting a few HTML attributes for the input. Note that all HTML5 attributes are supported by this API, with just a few listed in this case for brevity:
```
echo $ei->Form->input([ 'this-input',
	[
		'type' => 'text',
		'label'	=> 'This Input is Awesome!!',
		'group' => 'totally-different-group',
		'attr' => [
			'id' => 'MyInput',
			'class' => 'a series of classes'
		],
	]
]);
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

## HTML5 Form Elements
Easy Inputs was built to be compatible with all HTML5 form elements including:
* Color
* Date
* Datetime-local
* Email
* Month
* Number
* Range
* Search
* Tel
* Time
* URL
* Week