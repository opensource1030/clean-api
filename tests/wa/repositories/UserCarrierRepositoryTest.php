<?php
namespace WA\Testing\Repo;

use WA\Testing\TestCase;

class UserCarrierRepositoryTest extends TestCase {


    private $userCarrierRepo;


    public function setUp() {
        parent::setUp();
        $this->userCarrierRepo = $this->app->make('WA\Repositories\UserCarrierRepository');
    }

    public function testImplementsBaseRepositoryInterface() {
        $this->assertInstanceOf('WA\Repositories\BaseRepository', $this->userCarrierRepo);
    }
}
