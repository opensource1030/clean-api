<?php
namespace WA\DataStore;


use WA\Testing\TestCase;
use Way\Tests\ModelHelpers;

class CarrierDumpTest extends TestCase
{
    use ModelHelpers;

    private $className = 'WA\DataStore\CarrierDump';

    /**
     * @var WA\DataStore\CarrierDump
     */
    private $carrierDump;


    public function setUp()
    {
        parent::setUp();
        $this->carrierDump = $this->app->make($this->className);

        $this->loadFixture();
    }


    public function testBelongsToRelationships()
    {
        $this->assertbelongsto('dump', $this->className);

        $this->assertBelongsTo('jobstatus', $this->className);

        $this->assertBelongsTo('dataMap', $this->className);
    }


    public function testFindsMatchingDump()
    {
        $this->markTestSkipped('Broken fixture');
        $dumpPath = base_path() . "/tests/_data/fixtures/bruker_att_analogic_vzw_carrier_and_dumps.sql";

        $this->loadFixture($dumpPath);
        $this->carrierDump->dumpId = 1;

        $dataType = 'ivd';
        $result = $this->carrierDump->findMatchingDump($dataType);

        $this->assertNotEmpty($result);
    }

}
