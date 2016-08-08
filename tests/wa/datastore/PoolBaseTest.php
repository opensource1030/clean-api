<?php
namespace WA\DataStore;

use WA\Testing\TestCase;
use Way\Tests\ModelHelpers;

class PoolBaseTest extends TestCase
{

    use ModelHelpers;

    private $className = 'WA\DataStore\PoolBase';

    public function setUp()
    {
        parent::setUp();
    }


    public function testBelongsToRelationships()
    {
        $this->assertBelongsTo('poolGroup', $this->className);
    }
}
