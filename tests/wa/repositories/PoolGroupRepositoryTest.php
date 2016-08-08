<?php
namespace WA\Testing\Repo;

use WA\Testing\TestCase;

class PoolGroupRepositoryTest extends TestCase {

    protected $useCleanDatabase = 'sqlite';
    private $poolGroupRepo;

    public function setUp() {
        parent::setUp();
        $this->poolGroupRepo = $this->app->make('WA\Repositories\PoolGroupRepositoryInterface');
    }


    public function testFinders() {
        $carrier = \WA\DataStore\Carrier\Carrier::find(1);

        // Find by Carrier (Verizon)
        $groups = $this->poolGroupRepo->getAllByCarrier($carrier)->first();
        $this->assertInstanceOf('WA\DataStore\PoolGroup', $groups);
        $this->assertEquals('VZWVoice', $groups->code);

        // Find by code (should yield the same)
        $byCode = $this->poolGroupRepo->findByCode('VZWVoice');
        $this->assertEquals($groups, $byCode);


    }

    public function testImplementsBaseRepositoryInterface() {
        $this->assertInstanceOf('WA\Repositories\BaseRepository', $this->poolGroupRepo);
    }

}
