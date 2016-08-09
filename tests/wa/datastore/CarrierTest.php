<?php
namespace WA\DataStore;

use WA\Testing\TestCase;
use Way\Tests\ModelHelpers;

class CarrierTest extends TestCase
{
    use ModelHelpers;

    private $className = 'WA\DataStore\Carrier\Carrier';

    public function testHasManyRelationships()
    {
        $this->assertHasMany('accountSummaries', $this->className);

        $this->assertHasMany('lineSummaries', $this->className);

        $this->assertHasMany('users', $this->className);

        $this->assertHasMany('dumps', $this->className);

        $this->assertHasMany('wirelessLineDetails', $this->className);

        $this->assertHasMany('dataMaps', $this->className);

        $this->assertHasMany('currentCharges', $this->className);


    }


    public function testBelongsToManyRelationships()
    {
        $this->assertBelongsToMany('adjustables', $this->className);

       // $this->assertBelongsToMany('companies', $this->className);
    }
}


