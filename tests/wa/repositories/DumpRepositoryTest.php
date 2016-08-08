<?php

namespace WA\Testing\Repo;

use WA\Testing\TestCase;

class DumpRepositoryTest extends TestCase {
    protected $useCleanDatabase = 'sqlite';
    protected $repository = NULL;

    public function setUp() {
        parent::setUp();
        $this->markTestSkipped('Broken fixtures');
        $this->seedDumpData();
        $this->repository = \App::make('WA\Repositories\DumpRepositoryInterface');
    }

    public function testGetAll() {
        $all = $this->repository->getAll();
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection',$all);
        $this->assertEquals(1, $all->count());
    }

    /**
     * @TODO: This isn't a very useful test...
     */
    public function testGetAllStagedProcessedOrFailed() {
        $staged = $this->repository->getAllStagedDumps();
        $processed = $this->repository->getAllStagedDumps();
        $failed = $this->repository->getAllFailedDumps();

        $this->assertEquals(1,  $staged->count());
        $this->assertEquals(1,  $processed->count());
        $this->assertEquals(0,  $failed->count());
    }


    public function testFindById() {
        $byId = $this->repository->find(1);
        $this->assertTrue($byId instanceOf \WA\DataStore\Dump);
    }

    public function testFailtoFindById() {
        $byId = $this->repository->find(10);
        $this->assertNull($byId);
    }

    public function testCreate() {
        $data =
            [
                'directoryPath' => '/var/foo/foo',
                'companyId'      => 2,
                'carrierId'     => 2,
                'billEndDate'   => '2013-08-02',
                'statusId'   => 1
            ];
        $newDump = $this->repository->create($data);
        $this->assertEquals($newDump->directoryPath, '/var/foo/foo');
    }

   // @TODO: Tests for the dump retrieval methods (getting staged, processed, etc)

    protected function seedDumpData() {
        $sql = file_get_contents(base_path() . "/tests/_data/fixtures/babcock_att_loaded.sql");
        // $sql .= file_get_contents(base_path() . "/tests/_data/fixtures/babcock_att_raw_ivd.sql");
        \DB::unprepared($sql);
    }
}
