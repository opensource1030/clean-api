<?php
namespace WA\DataStore;

use WA\Testing\TestCase;
use Way\Tests\ModelHelpers;

class PoolGroupTest extends TestCase
{
    use ModelHelpers;

    private $className = 'WA\DataStore\PoolGroup';

    /**
     * @var WA\DataStore\PoolGroup
     */
    protected $poolGroup;

    public function setUp()
    {
        parent::setUp();
        $this->poolGroup = $this->app->make($this->className);
    }


    public function testBelongsToManyRelationships()
    {
        $this->assertBelongsToMany('companies', $this->className);
    }
}
