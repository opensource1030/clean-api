<?php

use Laravel\Lumen\Testing\DatabaseTransactions;

class AllocationsApiTest extends TestCase
{

    use DatabaseTransactions;

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

       $allocatedCharge = number_format($allocation->totalAllocatedCharge, 2);
       $servicePlanCharge =  number_format($allocation->servicePlanCharges, 2);
       $usageCharge = number_format($allocation->usageCharges, 2);
       $otherCharge = number_format($allocation->otherCharges, 2);
       $fees = number_format($allocation->fees, 2);

        $this->get('/allocations/'. $allocation->id)
            ->seeJson([
                'type' => 'allocations',
                'bill_month'=> $allocation->billMonth,
                'carrier' => $allocation->carrier,
                'currency' => $allocation->currency,
                'device' => $allocation->handsetModel,
                'allocated_charge' => "$allocatedCharge",
                'service_plan_charge' => "$servicePlanCharge",
                'usage_charge' => "$usageCharge",
                'other_charge' => "$otherCharge",
                'fees' => "$fees",
            ]);

    }

}