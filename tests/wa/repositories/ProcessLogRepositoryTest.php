<?php
namespace WA\Testing\Repo;

use WA\Testing\TestCase;
class ProcessLogRepositoryTest extends TestCase {

    private $processLogRepo;

    public function setUp() {
        parent::setUp();
        $this->processLogRepo = $this->app->make('WA\Repositories\ProcessLogRepository');
    }

    public function testImplementsBaseRepositoryInterface() {
        $this->assertInstanceOf('WA\Repositories\BaseRepository', $this->processLogRepo);
    }

}
