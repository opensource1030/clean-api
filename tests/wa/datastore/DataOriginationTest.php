<?php
namespace WA\DataStore;

use WA\Testing\TestCase;
use Way\Tests\ModelHelpers;

class DataOriginationTest extends TestCase
{

    use ModelHelpers;

    protected $dataOrigination;

    private $className = 'WA\DataStore\DataOrigination';

    public function setUp()
    {
        parent::setUp();
        $this->dataOrigination = $this->app->make($this->className);
    }


    public function testHasManyRelationships()
    {
        $this->assertHasMany('wirelessLineDetails', $this->className);
    }


}

