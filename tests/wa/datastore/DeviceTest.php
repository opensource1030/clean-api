<?php
namespace WA\DataStore;

use WA\Testing\TestCase;
use Way\Tests\ModelHelpers;

class DeviceTest extends TestCase {

    use ModelHelpers;

    protected $device;

    private $className = 'WA\DataStore\Device\Device';

    public function setUp()
    {
        parent::setUp();

        $this->device =  $this->app->make($this->className);

    }

    public function testBelongsToManyRelationships()
    {
        $this->assertBelongsToMany('employees', $this->className);

        $this->assertBelongsToMany('assets', $this->className);
    }



}
