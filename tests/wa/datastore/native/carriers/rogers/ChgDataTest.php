<?php

namespace WA\Testing\DataStore\Native\Carries\Rogers;

use WA\Testing\TestCase;
class ChgDataTest extends TestCase
{
    /*
     * @var ChgData $chgData
     */
    protected $chgData;
    protected $tableName = 'native_rogers_chg_test';

    public function setUp()
    {
        parent::setUp();
        $this->chgData = $this->app->make('WA\DataStore\Native\Carriers\Rogers\ChgData', [$this->tableName]);
    }

    public function testGetNativeTable()
    {
        $this->assertEquals($this->tableName, $this->chgData->getNativeTable());
    }

}
