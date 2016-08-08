<?php
namespace WA\Services\Cache;

use WA\Testing\TestCase;

class CacheTest extends TestCase
{

    private $cacheKey;
    private $cacheManager;

    /**
     * @var \WA\Services\Cache\Cache
     */
    protected $cache;


    public function setUp()
    {
        parent::setUp();
        $this->markTestSkipped("Deprecated, old cache implementation");
   }


    public function testReturnsTheGivenKeyFromCache()
    {
        $this->cacheManager->shouldReceive('section')->with($this->cacheKey)
                           ->andReturn($this->cacheManager->shouldReceive('get')->andReturn('xxKey')->getMock())->once(
            );

        $this->assertEquals('xxKey', $this->cache->get('xxKey'));

    }

    public function testAddsDataToCache()
    {
        $dataStore = [];

        $this->cacheManager->shouldReceive('section')->with($this->cacheKey)
                           ->andReturn(
                           \Mockery::self()->shouldReceive('put')
                                   ->with('someKey', $data = ['value' => 'something'], 2)
                                   ->andReturn($dataStore[] = $data)->getMock()
            )->once();

        $this->assertArrayHasKey('value', $this->cache->put('someKey', $data = ['value' => 'something'], 2));
    }


    public function testTakesPaginationIntoAccountWhenCachingData()
    {
        $this->cacheManager->shouldReceive('section')->andReturn(
                           $this->cacheManager->shouldReceive('put')->andReturn(true)->getMock()
        )->once();

        $result = $this->cache->putPaginated(1, 10, 23, 2, 'sampleKey');

        $this->assertObjectHasAttribute('currentPage', $result);
        $this->assertObjectHasAttribute('items', $result);
        $this->assertObjectHasAttribute('totalItems', $result);
        $this->assertObjectHasAttribute('perPage', $result);

    }


    public function testHasKeyForCache()
    {
        $this->cacheManager->shouldReceive('section')
                           ->andReturn(
                           $this->cacheManager->shouldReceive('has')->andReturn('myKey')->getMock()
            );

        $this->assertEquals('myKey', $this->cache->has('myKey'));
    }

}
