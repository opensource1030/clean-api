<?php
namespace WA\DataStore;

use WA\Testing\TestCase;
use Way\Tests\ModelHelpers;

class AdjustableTest extends TestCase {

    use ModelHelpers;

    public function testBelongsToManyAdjustments()
    {
        $this->assertBelongsTo('adjustment', 'WA\DataStore\Adjustable');
    }

}
 