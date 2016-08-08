<?php
namespace WA\Testing\DataStore\Native\Carriers\Verizon;

use WA\Testing\TestCase;


class CdrDataTest extends TestCase
{

    /**
     * @var \WA\DataStore\Native\Carriers\Verizon\CdrData
     */
    protected $cdrData;

    private $className = 'WA\DataStore\Native\Carriers\Verizon\CdrData';

    private $tableName;

    public function setUp()
    {
        $this->tableName = 'native_vzw_test_cdr';

        $this->cdrData = new $this->className($this->tableName);
    }

    public function testGetsNativeTable()
    {
        $this->assertEquals($this->tableName, $this->cdrData->getNativeTable());
    }


}
