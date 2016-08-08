<?php
namespace WA\DataStore;

use WA\Testing\TestCase;
use Way\Tests\ModelHelpers;

class ReportLineItemTest extends TestCase
{
    use ModelHelpers;

    private $className = 'WA\DataStore\ReportLineItem';

    protected $reportLineItem;

    public function setUp()
    {
        parent::setUp();

        $this->reportLineItem = $this->app->make($this->className);
    }

    public function testHasManyRelationships()
    {
        $this->assertHasMany('wirelessLineDetails', $this->className);
    }
}
