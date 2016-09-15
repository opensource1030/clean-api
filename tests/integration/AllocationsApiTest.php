<?php

//use Laravel\Lumen\Testing\DatabaseTransactions;
use Laravel\Lumen\Testing\DatabaseMigrations;

use WA\DataStore\Allocation\Allocation;

class AllocationsApiTest extends TestCase
{

    //use DatabaseTransactions;
    use DatabaseMigrations;


    /**
     * A basic functional test for allocations endpoints
     *
     *
     */
    public function testGetAllocations()
    {
        $allocation = factory(\WA\DataStore\Allocation\Allocation::class)->create();

        $this->get('/allocations/')
            ->seeJsonStructure([
                'data' => [
                    0 => [ 'type','id',
                        'attributes' => [
                            'bill_month', 'carrier', 'mobile_number', 'currency', 'device', 'allocated_charge', 'service_plan_charge', 'usage_charge', 'other_charge', 'fees'
                        ],
                        'links'
                    ]

                ]

            ]);
    }

    public function testGetAllocationById()
    {
        $allocation = factory(\WA\DataStore\Allocation\Allocation::class)->create();

        $this->get('/allocations/'. $allocation->id)
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