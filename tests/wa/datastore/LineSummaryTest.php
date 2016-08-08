<?php
namespace WA\DataStore;

use WA\Testing\TestCase;
use Way\Tests\ModelHelpers;

class LineSummaryTest extends TestCase {
    use ModelHelpers;

    private $className = 'WA\DataStore\LineSummary';


    public function testBelongsToRelationships()
    {
        $this->assertBelongsTo('accountSummary', $this->className);
    }

}
