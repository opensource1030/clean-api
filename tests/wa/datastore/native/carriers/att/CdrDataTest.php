<?php

namespace WA\Testing\DataStore\Native\Carriers\ATT;

use WA\Testing\TestCase;


class CdrDataTest extends TestCase
{

    private $className = "WA\DataStore\Native\Carriers\ATT\CdrData";

    private $tableName;

    /**
     * @var WA\DataStore\Native\Carriers\ATT\CdrData
     */
    protected $cdrData;


    public function setUp()
    {
        $this->tableName = 'test_cdr_table';

        $this->cdrData = new $this->className($this->tableName);
    }

    public function testGetsNativeData()
    {
        $this->assertEquals($this->tableName, $this->cdrData->getNativeTable());
    }
}
