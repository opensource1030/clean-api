<?php
namespace WA\DataStore;

use WA\Testing\TestCase;
use Way\Tests\ModelHelpers;
use Mockery;

class DataMapTest extends TestCase
{
    use ModelHelpers;

    /**
     * @var \WA\DataStore\DataMap
     */
    protected $dataMap;

    private $className = "WA\DataStore\DataMap";

    public function setUp()
    {
        parent::setUp();

        $this->dataMap = $this->app->make($this->className);

        $this->loadFixture();

    }


    public function testBelongsToRelationships()
    {
        $this->assertBelongsTo('carrier', $this->className);

        $this->assertBelongsTo('dataMapType', $this->className);

    }

    public function testBelongsToManyRelationships()
    {
        $this->assertBelongsToMany('dumps', $this->className);
    }

    public function testHasManyRelationships()
    {
        $this->assertHasMany('carrierDumps', $this->className);
    }

    public function testGetsCarrierHeaders()
    {
        $this->dataMap->carrierId = 1;

        $this->dataMap->dataMapType = Mockery::mock('WA\DataStore\DataMapType')
                                             ->shouldReceive(
                                             [
                                                 'whereShortname' => Mockery::mock('WA\DataStore\DataMapType')
                                                                            ->shouldReceive('first', 'getAttribute')
                                                                            ->andReturn(
                                                                            Mockery::mock('WA\DataStore\DataMapType')
                                                                                   ->shouldReceive(
                                                                                   'id',
                                                                                       'getAttribute'
                                                                                )->andReturn(1)->getMock()
                                                         )->getMock(),
                                                 'getAttribute' => true
                                             ]
            )->with('ivd');

        $this->dataMap->dataMapType = $this->dataMap->dataMapType->getMock();

        $this->assertEquals(1, $this->dataMap->carrierId);

        //$headers = $this->dataMap->getCarrierHeaders();

        //$this->assertJson($headers);
    }

    public function testGetsVersionId()
    {
        $result = $this->dataMap->getVersionId(1, 1);
        $this->assertEquals('verizon_ivd_20150118220142', $result);

    }

    public function testHasDataMap()
    {
        $result = $this->dataMap->hasDataMap(1);

        $this->assertTrue($result);
    }


    public function testScopesActiveDataMap()
    {
        $query = Mockery::mock('Illuminate\Database\Query\Builder')
                        ->shouldReceive('where')
                        ->andReturn(
                        Mockery::mock('Illuminate\Database\Query\Builder')
                               ->shouldReceive('where')
                               ->andReturn(true)
                               ->getMock()
            );

        $scoped = $this->dataMap->scopeActiveDataMap($query->getMock(), 1);

        $this->assertTrue($scoped);
    }


}

