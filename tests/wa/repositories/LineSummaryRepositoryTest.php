<?php
namespace WA\Testing\Repo;

use WA\Testing\TestCase;

class LineSummaryRepositoryTest extends TestCase {

    protected $useCleanDatabase = 'sqlite';

    protected $lineSummaryInstance;

    public function setUp() {
        parent::setUp();
        $this->lineSummaryInstance = $this->app->make('WA\Repositories\LineSummaryRepository');
    }

    public function testBoundToRepositoryInterface() {
        $this->assertInstanceOf('WA\Repositories\LineSummaryRepositoryInterface'
            , $this->lineSummaryInstance);
    }

}
