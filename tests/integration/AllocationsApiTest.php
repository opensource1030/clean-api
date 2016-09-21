<?php

use Laravel\Lumen\Testing\DatabaseMigrations;

use WA\DataStore\Allocation\Allocation;

class AllocationsApiTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * A basic functional test for Allocations
     *
     * @AD: last_update field needs to be revised in table and Allocation.
     */

    public function testGetAllocations()
    {
        $this->markTestIncomplete(
          'TODO: needs to be reviewed.' 
        );
        
        factory(\WA\DataStore\Allocation\Allocation::class, 40)->create();

        $this->json('GET', '/allocations/')
            ->seeJsonStructure(
            [
                'data' => [
                    0 => [ 
                        'type',
                        'id',
                        'attributes' => [
                            'bill_month',
                            'carrier',
                            'mobile_number',
                            'currency',
                            'device',
                            'allocated_charge',
                            'service_plan_charge',
                            'usage_charge',
                            'other_charge',
                            'fees',
                            'last_upgrade'

                        ],
                        'links' => [
                            'self'
                        ]
                    ]
                ]
            ]);
    }

    public function testGetAllocationById()
    {
        $this->markTestIncomplete(
          'TODO: needs to be reviewed.' 
        );

        $allocation = factory(\WA\DataStore\Allocation\Allocation::class)->create();

        $this->json('GET', '/allocations/'. $allocation->id)
            ->seeJson([
                'type' => 'allocations',
                'bill_month'=> $allocation->billMonth,
                'carrier' => $allocation->carrier,
                'currency' => $allocation->currency,
                'device' => $allocation->handsetModel,
                'allocated_charge' => "$allocation->totalAllocatedCharge",
                'service_plan_charge' => "$allocation->servicePlanCharges",
                'usage_charge' => "$allocation->usageCharges",
                'other_charge' => "$allocation->otherCharges",
                'fees' => "$allocation->fees",
            ]);
    }
}