<?php
namespace WA\Testing\Repo;

use WA\Testing\TestCase;
class CurrentChargeRepositoryTest extends TestCase {
    private $repository;

    public function setUp() {
        parent::setUp();
        $this->repository = $this->app->make('WA\Repositories\CurrentChargeRepository');
    }

    public function testBoundToRepositoryInterface() {
        $this->assertInstanceOf('WA\Repositories\CurrentChargeRepositoryInterface'
            , $this->repository);
    }
}
