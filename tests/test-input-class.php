<?php
/**
 * Class InputClassTest
 *
 * @package EasyInputs
 */
namespace EasyInputs;

/**
 * Test cases for the input.class.php file.
 */
class InputClassTest extends \WP_UnitTestCase
{

    /**
     * Minimal configuration for Inputs.
     */
    public $name    = 'InputClassTest';
    public $args     = [
        'type'      => 'text',
        'attrs'     => [
            'class' => 'InputTestClass test',
            'id'    => 'InputTestClass'
        ],
        'val'       => 1,
        'wrapper'   => '<p>%s</p>'
    ];
    public function __construct()
    {
        $this->ei = new EasyInputs([
            'name'  => $this->name
        ]);
    }
    
    /**
     * Return a valid field name attribute.
     */
    public function testFieldName()
    {
        $name   = ( new Input($name, $args, $this->ei->Form) )->fieldName();
        $this->assertTrue(is_array($name), 'fieldName function returned a ' . gettype($name)
            . ' instead of an array.');
    }
}
