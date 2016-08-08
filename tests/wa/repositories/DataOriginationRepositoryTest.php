<?php
namespace WA\Testing\Repo;

use WA\Testing\TestCase;
class DataOriginationRepositoryTest extends TestCase {

    protected $repoInstance;
    protected $useCleanDatabase = 'sqlite';


    public function setUp()
    {
        parent::setUp();
        $this->repoInstance = \App::make('WA\Repositories\DataOriginationRepositoryInterface');

    }

    public function testGetOriginationIdByName()
    {
        $dataId = $this->repoInstance->getOriginationIdByName('carrier-data');
        $this->assertEquals(1,$dataId);
    }


}
