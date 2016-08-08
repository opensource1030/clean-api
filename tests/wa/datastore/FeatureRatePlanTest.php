<?php
namespace WA\DataStore;

use WA\Testing\TestCase;
use Way\Tests\ModelHelpers;

class FeatureRatePlanTest extends TestCase
{

    use ModelHelpers;

    private $className = 'WA\DataStore\FeatureRatePlan';

    /**
     * @var WA\DataStore\FeatureRatePlan
     */
    protected $featureRatePlan;

    public function setUp()
    {
        parent::setUp();
        $this->featureRatePlan = $this->app->make($this->className);
    }


    public function testBelongsToRelationships()
    {

        $this->assertBelongsTo('poolgroup', $this->className);

    }

    public function testHasManyRelationships()
    {
        $this->assertHasMany('invoices', $this->className);
        $this->assertHasMany('wirelessLineDetails', $this->className);

    }


}


