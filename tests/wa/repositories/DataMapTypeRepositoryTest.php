<?php
namespace WA\Testing\Repo;

use WA\Testing\TestCase;
class DataMapTypeRepositoryTest extends TestCase {
    protected $useCleanDatabase = 'sqlite';
    protected $repository = NULL;

    public function setUp() {
        parent::setUp();
        $this->repository = \App::make('WA\Repositories\DataMapTypeRepositoryInterface');
    }


    public function testGetAll() {
        $all = $this->repository->getAll();
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection',$all);
        $this->assertEquals(10, $all->count());
    }

    public function testGetByName() {
        $byName = $this->repository->getByName('ivd');
        $this->assertInstanceOf('WA\DataStore\DataMapType',$byName);
    }
}
