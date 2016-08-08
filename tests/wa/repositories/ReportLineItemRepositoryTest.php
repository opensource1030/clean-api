<?php

namespace WA\Testing\Repo;

use WA\Testing\TestCase;

class ReportLineItemRepositoryTest extends TestCase {

    protected $useCleanDatabase = 'sqlite';

    private $reportLineItemRepo;

    public function setUp() {
        parent::setUp();
        $this->reportLineItemRepo = $this->app->make('WA\Repositories\ReportLineItemRepository');
    }


    public function testGetIDByName() {
        //$rli = $this->reportLineItemRepo->getLineItemIdByName('RatePlans');
        //$this->assertGreaterThan(1, $rli);

        $rli = $this->reportLineItemRepo->getLineItemIdByName('ZZZZDomPooledDataRPD');
        $this->assertNull($rli);
    }

    public function testImplementsBaseRepositoryInterface() {
        $this->assertInstanceOf('WA\Repositories\BaseRepository', $this->reportLineItemRepo);
    }

}
 
