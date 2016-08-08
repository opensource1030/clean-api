<?php
namespace WA\Testing\Repo;

use WA\Testing\TestCase;

class InvoiceRepositoryTest extends TestCase {
    protected $useCleanDatabase = 'sqlite';
    protected $repository = NULL;

    public function setUp() {
        $this->markTestSkipped('Broken fixture');
        parent::setUp();
        $this->repository = \App::make('WA\Repositories\InvoiceRepository');
        $this->seedData();
    }

    protected function seedData()
    {
        self::loadFixture(base_path() . "/tests/_data/fixtures/babcock_att_loaded.sql");
        self::loadFixture(base_path() . '/tests/_data/fixtures/babcock_att_consolidated_ivd.sql');
    }

    public function testGetAll() {
        $all = $this->repository->getAll();
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection',$all);
        $this->assertEquals(26, $all->count());
    }

    public function testFindById() {
        $invd = $this->repository->getAll()->first();
        $this->assertTrue($invd instanceOf \WA\DataStore\Invoice);
    }

    public function testGetAllDataByLine() {
        $allData = $this->repository->allDataByLine("5022954516");
        $this->assertEquals(25, $allData->count());
    }

    public function testAllDataByLineByDumpAccount() {
        $dump = \WA\DataStore\Dump::first();
        $data = $this->repository->allDataByLineByDumpAccount(
                "5022954516",
                $dump,
                "823448162")->get();
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection',$data);
        $this->assertEquals(25,$data->count());

    }

    public function testGetLinesByAccountNumber() {
        $dump = \WA\DataStore\Dump::first();
        $res = $this->repository->getLinesByAccountNumber("823448162",$dump);
        $this->assertEquals(1,$res->count());
    }

    public function testPurge() {
        $before = $this->repository->getAll()->count();
        $res = $this->repository->purgeByDumpId(1);
        $this->assertEquals($before,$res);
    }

    public function testLineCount() {
        $this->assertEquals(1, $this->repository->lineCountByInvoiceNumber("8512848"));
    }
}
