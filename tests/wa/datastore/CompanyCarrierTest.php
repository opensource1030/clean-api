<?php
namespace WA\DataStore;

use WA\Testing\TestCase;
use Way\Tests\ModelHelpers;

class CompanyCarrierTest extends TestCase {
    use ModelHelpers;

    private $className = 'WA\DataStore\CompanyCarrier';


    public function testBelongsToRelationships()
    {
        $this->assertBelongsTo('carrier', $this->className);

        $this->assertBelongsTo('company', $this->className);
    }


}
