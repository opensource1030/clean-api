<?php
namespace WA\Testing\Repo;

use WA\Testing\TestCase;

class JobStatusRepositoryTest extends TestCase {
    protected $useCleanDatabase = 'sqlite';

    public function setUp() {
        parent::setUp();
        $this->repository = $this->app->make('WA\Repositories\JobStatusRepositoryInterface');
    }

    public function testFindByName() {
        $res = $this->repository->findByName('Data Consolidation Complete');
        $this->assertInstanceOf('WA\DataStore\JobStatus', $res);
    }
}
