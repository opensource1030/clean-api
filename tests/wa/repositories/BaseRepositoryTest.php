<?php
namespace WA\Testing\Repo;

use WA\Testing\TestCase;

class BaseRepositoryTest extends TestCase {

    protected $repository = NULL;

    protected $useCleanDatabase = 'sqlite';

    /*
     * @TODO: Need to test a lot of failures here
     */
    public function setUp() {
        parent::setUp();
        // We'll use a well-seeded repo for testing
        $this->repository = \App::make('WA\Repositories\FeatureRatePlanRepositoryInterface');
    }

    public function testGetDataStore() {
        $ds = $this->repository->getDataStore();
        $this->assertInstanceOf('WA\DataStore\FeatureRatePlan',$ds);
    }

    public function testGetByKey() {
        $plan = $this->repository->getByKey(20);
	//        $this->assertInstanceOf('WA\DataStore\FeatureRatePlan',$plan);
    }

    public function testPagination() {
        $this->markTestSkipped("No longer using this pagination library");
        $paginated = $this->repository->togglePagination(20)->getAll();
        $this->assertInstanceOf('Illuminate\Pagination\Paginator',$paginated);
	//        $this->assertEquals(20,$paginated->count());
    }

    public function testToggleCache() {
        $res = $this->repository->toggleCache(30);
        $this->assertInstanceOf('WA\Repositories\FeatureRatePlanRepository',$res);
    }

    public function testUpdateRecord() {
      /**        $record = $this->repository->getByKey(5);
        $newValues = [ 'name' => 'TEST PLAN'];
        $newRecord = $this->repository->update($record,$newValues);
        $this->assertTrue($newRecord); **/
    }

    public function testDeleteRecord() {
        $record = $this->repository->getByKey(20);
        $res = $this->repository->delete($record);
	//        $this->assertTrue($res);
    }

    public function testGetNew() {
        $new = $this->repository->getNew();
        $this->assertInstanceOf('WA\DataStore\FeatureRatePlan',$new);
    }

    public function testGetAll() {
        $all = $this->repository->getAll();
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection',$all);
    }

    public function testPurgeByDumpID() {
        $this->markTestIncomplete('Incomplete');
    }

}

