<?php
namespace WA\DataStore;

use WA\Testing\TestCase;
use Way\Tests\ModelHelpers;

class DataMapTypeTest extends TestCase
{
   use ModelHelpers;

    protected $dataMapType;

    private $className = 'WA\DataStore\DataMapType';


    public function setUp()
    {
        parent::setUp();

        $this->dataMapType = $this->app->make($this->className);
    }

    public function testHasManyRelationships()
    {
        $this->assertHasMany('dataMap', $this->className);
    }


}
 