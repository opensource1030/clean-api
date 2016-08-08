<?php
namespace WA\DataStore;

use WA\Testing\TestCase;
use Way\Tests\ModelHelpers;

class AdjustmentTest extends TestCase {
    use ModelHelpers;

    protected $userDatabase = true;


    public function testHasManyAdjustabales()
    {
        $this->assertHasMany('adjustables', 'WA\DataStore\Adjustment');
    }

}
 