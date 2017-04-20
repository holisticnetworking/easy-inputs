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
}
