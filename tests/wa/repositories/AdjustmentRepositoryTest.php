<?php
namespace WA\Testing\Repo;

use WA\Testing\TestCase;
class AdjustmentRepositoryTest extends TestCase {

    protected $useCleanDatabase = 'sqlite';

    protected $repository;


    public function setUp() {
        parent::setUp();
        $this->repository = \App::Make('WA\Repositories\AdjustmentRepository');
    }

    /**
     * Grab all the grouping of adjustables by the adjustment name
     */
    public function testFindByName() {
        $adjustablesCollection = $this->repository->findByName('att-ivd');
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $adjustablesCollection);
    }


    public function testReturnsNullOnNonExisitnAdjutsmentGroup() {
        $nonExisting = $this->repository->findByName('yada-yada');
        $this->assertNull($nonExisting);
    }
}
