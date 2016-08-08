<?php
namespace WA\Testing\Repo;

use WA\Testing\TestCase;

class AssetRepositoryTest extends TestCase {

    protected $repository;
    protected $useCleanDatabase = 'sqlite';

    public function setUp() {
        parent::setUp();
        $this->markTestSkipped("Integrity constraint violation: 19 NOT NULL constraint failed: assets.isActive");
        $this->repository = \App::Make('WA\Repositories\AssetRepositoryInterface');
    }

    public function testMatchIdentificationToUserWithoutExistingUser() {
        $newAsset = $this->repository->matchIdentificationToUser('5555555555');
        $this->assertInstanceOf('WA\DataStore\Asset\Asset',$newAsset);
        $this->assertEquals('5555555555',$newAsset->identification);
    }


    public function testMatchIdentificationToUserWithExistingUser() {
        $this->markTestIncomplete('Need to work on clearing cache between these requests.');
    }



}
