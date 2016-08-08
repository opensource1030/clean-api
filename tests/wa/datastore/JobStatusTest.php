<?php
namespace WA\DataStore;

use WA\Testing\TestCase;
use Way\Tests\ModelHelpers;

class JobStatusTest extends TestCase {
    use ModelHelpers;

    private $className = 'WA\DataStore\JobStatus';

    protected $jobstatus;

    public function setUp()
    {
        parent::setUp();
        $this->jobstatus = $this->app->make($this->className);
    }

    public function testHasManyRelationships()
    {
        $this->assertHasMany('dump', $this->className);
        $this->assertHasMany('carrierDumps', $this->className);
    }
}
