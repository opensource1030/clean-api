<?php
namespace WA\Testing\Repo;

use WA\Testing\TestCase;

class FeatureRatePlanRepositoryTest extends TestCase {
    protected $useCleanDatabase = 'sqlite';
    protected $featureRatePlan;

    public function setUp() {
        parent::setUp();
        $this->featureRatePlan = $this->app->make('WA\Repositories\FeatureRatePlanRepository');
    }

    public function testGetIncomplete() {
        $this->markTestSkipped("Need to migrate to check for the DataGrid collection");
        $incomplete = $this->featureRatePlan->getIncomplete();
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection',$incomplete);
    }

    public function testFindPlanByName() {
        $plan = $this->featureRatePlan->findPlanByName('NATIONWIDE BUS 4000 EMAIL+MSG');
        // Should grab the (first) unpooled plan
	//        $this->assertInstanceOf('WA\DataStore\FeatureRatePlan', $plan);
	//	        $this->assertEquals(0, $plan->poolGroupId);

    }

    public function testFindPlanByNameWithCost() {
        $plan = $this->featureRatePlan->findPlanByName('NATIONWIDE BUS 4000 EMAIL+MSG', '194.99');
        // This is the pooled plan
	//        $this->assertEquals(3, $plan->poolGroupId);
    }

    public function testFindPlanByNamePartialMonthCostInComments() {
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );

        $plan = $this->featureRatePlan->findPlanByName('NATIONWIDE BUS 4000 EMAIL+MSG', '194.99');
        $this->assertEquals(3, $plan->poolGroupId);
    }

    public function testRepositoryIsBoundToInterface() {
        $this->assertInstanceOf('WA\Repositories\FeatureRatePlanRepositoryInterface', $this->featureRatePlan);
    }

    public function testReturnTrueIfKicker() {
      //        $this->assertTrue($this->featureRatePlan->isKicker('Credit for 2GB data for 4G LTE Smartphones'));
    }

    public function testReturnFalseIfNotKicker() {
      //        $this->assertFalse($this->featureRatePlan->isKicker('DataPro 4GB for Smartphone'));
    }

    public function testReturnTrueIfIsDiscountable() {
      //        $this->assertTrue($this->featureRatePlan->isDiscountable('BMNBIBPNTN1350UNWUMM'));
    }

    public function testReturnFalseIfFeatureDoesNotExistOrIsNotDiscountable() {
      //        $this->assertFalse($this->featureRatePlan->isDiscountable('NO FEATURE'));
    }

}
