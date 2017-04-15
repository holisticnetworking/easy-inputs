<?php
/**
 * Class EasyInputsTest
 *
 * @package EasyInputs
 */
namespace EasyInputs;

use phpmock\phpunit\PHPMock;

/**
 * Test cases for the easy-inputs.php file.
 */
class EasyInputsTest extends \WP_UnitTestCase
{
    use PHPMock;
    
    public function __construct()
    {
        $this->ei = new EasyInputs([
            'name'  => 'TestingEasyInputs'
        ]);
    }

    /**
     * Make sure we always get a string back.
     */
    function testAttrsToString()
    {
        $attributes = $this->ei->attrsToString(['class' => 'bubblebutt']);
        $this->assertTrue(is_string($attributes), "Attributes returned a " . gettype($attributes)
            . " instead of a string");
    }
    
    /**
     * Make sure we always get a string back.
     */
    function testHiddenFields()
    {
        $fields = $this->ei->hiddenFields('TestSetting');
        $this->assertTrue(is_string($fields), "Fields returned a " . gettype($fields)
            . " instead of a string");
    }
}
