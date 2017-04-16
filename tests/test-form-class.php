<?php
/**
 * Class FormClassTest
 *
 * @package EasyInputs
 */
namespace EasyInputs;

/**
 * Test cases for the form.class.php file.
 */
class FormClassTest extends \WP_UnitTestCase
{

    public function __construct()
    {
        $this->ei = new EasyInputs([
            'name'  => 'TestingEasyInputs'
        ]);
    }
    
    /**
     * Should always return a string.
     */
    public function testOpen()
    {
        $open   = $this->ei->Form->open();
        $this->assertTrue(is_string($open), 'Open function returned a ' . gettype($open)
            . ' instead of a string.');
    }
    /**
     * Should always return a string.
     */
    public function testClose()
    {
        $close   = $this->ei->Form->close();
        $this->assertTrue(is_string($close), 'Close function returned a ' . gettype($close)
            . ' instead of a string.');
    }
    
    
    /**
     * Should always return a string.
     */
    public function testFieldsetOpen()
    {
        $fieldset   = $this->ei->Form->fieldsetOpen(['attrs' => ['class' => 'testFieldSet']]);
        $this->assertTrue(is_string($fieldset), 'FieldsetOpen() function returned a ' . gettype($fieldset)
            . ' instead of a string.');
    }
    /**
     * Should always return a string.
     */
    public function testFieldsetClose()
    {
        $fieldset   = $this->ei->Form->fieldsetClose();
        $this->assertTrue(is_string($fieldset), 'FieldsetClose() function returned a ' . gettype($fieldset)
            . ' instead of a string.');
    }
    
    /**
     * Should always return a string.
     */
    public function testLegend()
    {
        $legend   = $this->ei->Form->legend([
            'title' => 'I am Test Legend',
            'attrs' => ['class' => 'testLegend']
        ]);
        $this->assertTrue(is_string($legend), 'Legend function returned a ' . gettype($legend)
            . ' instead of a string when passed an array.');
    }
    /**
     * Should always return a string.
     */
    public function testLegendString()
    {
        $legend   = $this->ei->Form->legend('I am Test Legend');
        $this->assertTrue(is_string($legend), 'Legend function returned a ' . gettype($legend)
            . ' instead of a string when passed a string.');
    }
    
    /**
     * Should always return a string.
     */
    public function testLabel()
    {
        $label   = $this->ei->Form->label('test_label', 'I am Test Label', ['class' => 'testLabel']);
        $this->assertTrue(is_string($label), 'Label function returned a ' . gettype($label)
            . ' instead of a string.');
    }
    
    
    
    
    /**
     * Should always return an array.
     */
    public function testSetFieldsetDefaultsEmpty()
    {
        $defaults   = $this->ei->Form->setFieldsetDefaults();
        $this->assertTrue(is_array($defaults), 'setFieldsetDefaults function returned a ' . gettype($defaults)
            . ' instead of an array when called without arguments.');
    }
    /**
     * Should always return an array.
     */
    public function testSetFieldsetDefaultsArray()
    {
        $defaults   = $this->ei->Form->setFieldsetDefaults(['legend' => 'Default Legend']);
        $this->assertTrue(is_array($defaults), 'setFieldsetDefaults function returned a ' . gettype($defaults)
            . ' instead of an array when passed an array.');
    }
    
    /**
     * Should always return a string.
     */
    public function testSplitGroup()
    {
        $group   = $this->ei->Form->splitGroup('test,groups');
        $this->assertTrue(is_array($group), 'setFieldsetDefaults function returned a ' . gettype($group)
            . ' instead of an array.');
    }
    /**
     * Should always return a string.
     */
    public function testSplitGroupArray()
    {
        $group   = $this->ei->Form->splitGroup(['test','groups']);
        $this->assertTrue(is_array($group), 'setFieldsetDefaults function returned a ' . gettype($group)
            . ' instead of an array.');
    }
}
