<?php
namespace WA\DataStore;

use Mockery;
use WA\Testing\TestCase;
use Way\Tests\ModelHelpers;

class DumpTest extends TestCase
{
    use ModelHelpers;

    /**
     * @var WA\DataStore\Dump
     */
    protected $dump;
    private $className = 'WA\DataStore\Dump';

    public function setUp()
    {
        parent::setUp();

        $this->loadFixture();
        $this->dump = $this->app->make($this->className);
    }


    public function testBelongsToRelationships()
    {
        $this->assertBelongsTo('jobstatus', $this->className);
        $this->assertBelongsTo('carrier', $this->className);
        $this->assertBelongsTo('company', $this->className);
    }

    public function testHasManyRelationships()
    {
        $this->assertHasMany('invoices', $this->className);
        $this->assertHasMany('accountSummaries', $this->className);
        $this->assertHasMany('lineSummaries', $this->className);
        $this->assertHasMany('carrierDumps', $this->className);
    }


    public function testGetsOrderedCarriedDumps()
    {
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $this->dump->getOrderedCarrierDumps());
    }


    public function testMapsDataStoreHeaders()
    {
        $randData = json_encode(
            ['head' => 'something', 'anotherHeader' => 'anotherThing', 'yetAnotherHead' => 'Some Random Thing']
        );

//        var_dump(json_decode($randData, true)); die;
        $dataMap = Mockery::mock('WA\DataStore\DataMap')
            ->shouldReceive('getCarrierHeaders')
            ->once()
            ->andReturn($randData);
        $response = $this->dump->mapDataStoreHeaders($dataMap->getMock(), 1);

        $this->assertInternalType('array', $response);

        $this->assertNotContains('something', $response);
        $this->assertNotContains('anotherThing', $response);
        $this->assertNotContains('Some Random Thing', $response);

        $this->assertContains('head', $response);
        $this->assertContains('anotherHeader', $response);
        $this->assertContains('yetAnotherHead', $response);
    }


}
