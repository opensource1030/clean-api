<?php
namespace WA\DataStore;

use WA\Testing\TestCase;
use Way\Tests\ModelHelpers;

class ConsolidatedCdrTest extends TestCase
{

    use ModelHelpers;

    protected $consolidateCdr;

    private $className = "WA\DataStore\ConsolidatedCdr";

    public function setUp()
    {
        parent::setUp();

        $this->consolidateCdr = $this->app->make($this->className);
    }


    public function testBelongsToRelationships()
    {
        $this->assertBelongsTo('dump', $this->className);

        $this->assertBelongsTo('company', $this->className);

        $this->assertBelongsTo('carrier', $this->className);
    }


}
