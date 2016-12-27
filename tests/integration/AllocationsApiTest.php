<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use WA\DataStore\Allocation\Allocation;

class AllocationsApiTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * A basic functional test for Allocations.
     *
     * @AD: last_update field needs to be revised in table and Allocation.
     */
    public function testGetAllocations()
    {
        $allocation = factory(\WA\DataStore\Allocation\Allocation::class)->create();
        $carrier1 = factory(\WA\DataStore\Carrier\Carrier::class)->create();
        $allocation->carriers()->associate($carrier1);
        $allocation->save();

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
                            'last_upgrade',

                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                ],
            ]);
    }

    public function testGetAllocationById()
    {
        $allocation = factory(\WA\DataStore\Allocation\Allocation::class)->create();
        $carrier1 = factory(\WA\DataStore\Carrier\Carrier::class)->create();
        $allocation->carriers()->associate($carrier1);
        $allocation->save();

        $this->json('GET', '/allocations/'.$allocation->id)
            ->seeJsonStructure(
                [
                    'data' => [
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
                                'last_upgrade',

                            ],
                            'links' => [
                                'self',
                            ],
                    ],
                ]);
    }
}
