<?php
namespace WA\DataStore;

use WA\Testing\TestCase;
use Way\Tests\ModelHelpers;

class WirelessLineDetailTest extends TestCase
{
    use ModelHelpers;

    /**
     * @var /WA\DataStore\WirelessLineDetail
     */
    protected $wirelessLineDetail;
    private $className = 'WA\DataStore\WirelessLineDetail';

    public function setUp()
    {
        parent::setUp();

        $this->wirelessLineDetail = $this->app->make($this->className);

    }

    public function testBelongToRelationships()
    {
        // $this->assertBelongsTo('employee', $this->className);
        $this->assertBelongsTo('carrier', $this->className);
        $this->assertBelongsTo('reportLineItem', $this->className);
        $this->assertBelongsTo('accountSummary', $this->className);
        $this->assertBelongsTo('asset', $this->className);
        $this->assertBelongsTo('featureRatePlan', $this->className);
        $this->assertBelongsTo('dataOrigination', $this->className);
    }
}
