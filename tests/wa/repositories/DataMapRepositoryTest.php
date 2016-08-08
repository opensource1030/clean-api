<?php

namespace WA\Testing\Repo;

use WA\Testing\TestCase;

class DataMapRepositoryTest extends TestCase {
    protected $useCleanDatabase = 'sqlite';

    protected $repository = NULL;

    public function setUp() {
        parent::setUp();
        $this->repository = \App::make('WA\Repositories\DataMapRepositoryInterface');
    }

    public function testGetAll() {
        $all = $this->repository->getAll();
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $all);
        $this->assertEquals(22, $all->count());
    }

    public function testGetActive() {
        $active = $this->repository->getActive();
        $this->assertEquals(22, $active->count());
    }


    public function testGetVersionId() {
        $versionId = $this->repository->getVersionId(1, 4);
        $this->assertEquals('verizon_als_20150118220142', $versionId);
    }


    /**
     * Test that there are only five types:  IVD, CDR, ALS, WLS, INV
     */
    public function testGetDataMapTypes() {
        $types = $this->repository->getDataMapTypes();
        $this->assertEquals('10', $types->count());

    }
}
