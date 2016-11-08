<?php

//use Laravel\Lumen\Testing\DatabaseTransactions;
use Laravel\Lumen\Testing\DatabaseMigrations;

use WA\DataStore\Addon\Addon;



class ServicesApiTest extends TestCase
{
    //use DatabaseTransactions;
    use DatabaseMigrations;

    /**
     * A basic functional test for services.
     */
    public function testGetServices()
    {
        factory(\WA\DataStore\Service\Service::class, 40)->create();

        $res = $this->get('services');

        $res->seeJsonStructure([
            'data' => [
                0 => [
                    'type',
                    'id',
                    'attributes' => [
                        'status',
                        'title',
                        'planCode',
                        'cost',
                        'description',
                        'domesticMinutes',
                        'domesticData',
                        'domesticMessages',
                        'internationalMinutes',
                        'internationalData',
                        'internationalMessages',
                        'carrierId',
                        'created_at' => [
                            'date',
                            'timezone_type',
                            'timezone',
                        ],
                        'updated_at' => [
                            'date',
                            'timezone_type',
                            'timezone',
                        ],
                    ],
                    'links' => [
                        'self',
                    ],
                ],
            ],
            'meta' => [
                'pagination' => [
                    'total',
                    'count',
                    'per_page',
                    'current_page',
                    'total_pages',
                ],
            ],
            'links' => [
                'self',
                'first',
                'next',
                'last',
            ],
        ]);
    }

    public function testGetServiceById()
    {
        $service = factory(\WA\DataStore\Service\Service::class)->create();

        $this->get('services/'.$service->id)
            ->seeJson([
                'status' => 'Enabled',
                'type' => 'services',
                'title' => $service->title,
                'planCode' => "$service->planCode",
                'cost' => "$service->cost",
                'description' => $service->description,
                'domesticMinutes' => "$service->domesticMinutes",
                'domesticData' => "$service->domesticData",
                'domesticMessages' => "$service->domesticMessages",
                'internationalMinutes' => "$service->internationalMinutes",
                'internationalData' => "$service->internationalData",
                'internationalMessages' => "$service->internationalMessages",
                'carrierId' => "$service->carrierId",
            ]);
        
    }

    public function testGetServiceByIdandIncludesAddons()
    {
        $service = factory(\WA\DataStore\Service\Service::class)->create();

        factory(\WA\DataStore\Addon\Addon::class)->create(['serviceId' => $service->id]);
        factory(\WA\DataStore\Addon\Addon::class)->create(['serviceId' => $service->id]);

        $response = $this->json('GET', 'services/'.$service->id.'?include=addons')
            ->seeJsonStructure([
                'data' => [
                    'type',
                    'id',
                    'attributes' => [
                        'status',
                        'title',
                        'planCode',
                        'cost',
                        'description',
                        'domesticMinutes',
                        'domesticData',
                        'domesticMessages',
                        'internationalMinutes',
                        'internationalData',
                        'internationalMessages',
                        'carrierId',
                        'created_at' => [
                            'date',
                            'timezone_type',
                            'timezone',
                        ],
                        'updated_at' => [
                            'date',
                            'timezone_type',
                            'timezone',
                        ],
                    ],
                    'links' => [
                        'self',
                    ],
                    'relationships' => [
                        'addons' => [
                            'links' => [
                                'self',
                                'related',
                            ],
                            'data' => [
                                0 => [
                                    'type',
                                    'id',
                                ],
                            ],
                        ],
                    ],
                ],
                'included' => [
                    1 => [
                        'type',
                        'id',
                        'attributes' => [
                            'name',
                            'cost',
                            'serviceId'
                        ],
                        'links' => [
                            'self',
                        ],
                    ],

                ],
            ]);
    }

    public function testCreateService()
    {
        $this->post('/services',
            [
                'data' => [
                    'type' => 'services',
                    'attributes' => [
                        'status' => 'Enabled',
                        'title' => 'Service Test',
                        'planCode' => '11111',
                        'cost' => '22',
                        'description' => 'Test Service',
                        'domesticMinutes' => '111',
                        'domesticData' => '222',
                        'domesticMessages' => '333',
                        'internationalMinutes' => '444',
                        'internationalData' => '555',
                        'internationalMessages' => '666',
                        'carrierId' => "1",
                    ],
                ],
            ])
            ->seeJson([
                'status' => 'Enabled',
                'type' => 'services',
                'title' => 'Service Test',
                'planCode' => '11111',
                'cost' => '22',
                'description' => 'Test Service',
                'domesticMinutes' => '111',
                'domesticData' => '222',
                'domesticMessages' => '333',
                'internationalMinutes' => '444',
                'internationalData' => '555',
                'internationalMessages' => '666',
                'carrierId' => "1",
            ]);
    }

    public function testUpdateService()
    {
        $service = factory(\WA\DataStore\Service\Service::class)->create(
            ['status' => 'Enabled', 'title' => 'title1', 'planCode' => 11111, 'cost' => 30, 'description' => 'desc1', 'domesticMinutes' => 100, 'domesticData' => 100, 'domesticMessages' => 100, 'internationalMinutes' => 100, 'internationalData' => 100, 'internationalMessages' => 100, 'carrierId' => "1"]
        );
        $serviceAux = factory(\WA\DataStore\Service\Service::class)->create(
            ['status' => 'Disabled', 'title' => 'title2', 'planCode' => 22222, 'cost' => 40, 'description' => 'desc2', 'domesticMinutes' => 200, 'domesticData' => 200, 'domesticMessages' => 200, 'internationalMinutes' => 200, 'internationalData' => 200, 'internationalMessages' => 200, 'carrierId' => "2"]
        );

        $this->assertNotEquals($service->status, $serviceAux->status);
        $this->assertNotEquals($service->id, $serviceAux->id);
        $this->assertNotEquals($service->title, $serviceAux->title);
        $this->assertNotEquals($service->cost, $serviceAux->cost);
        $this->assertNotEquals($service->description, $serviceAux->description);
        $this->assertNotEquals($service->domesticMinutes, $serviceAux->domesticMinutes);
        $this->assertNotEquals($service->domesticData, $serviceAux->domesticData);
        $this->assertNotEquals($service->domesticMessages, $serviceAux->domesticMessages);
        $this->assertNotEquals($service->internationalMinutes, $serviceAux->internationalMinutes);
        $this->assertNotEquals($service->internationalData, $serviceAux->internationalData);
        $this->assertNotEquals($service->internationalMessages, $serviceAux->internationalMessages);
        $this->assertNotEquals($service->carrierId, $serviceAux->carrierId);

        $this->put('/services/'.$serviceAux->id,
            [
                'data' => [
                    'type' => 'services',
                    'id' => $service->id,
                    'attributes' => [
                        'status' => $service->status,
                        'title' => "$service->title",
                        'planCode' => "$service->planCode",
                        'cost' => "$service->cost",
                        'description' => $service->description,
                        'domesticMinutes' => "$service->domesticMinutes",
                        'domesticData' => "$service->domesticData",
                        'domesticMessages' => "$service->domesticMessages",
                        'internationalMinutes' => "$service->internationalMinutes",
                        'internationalData' => "$service->internationalData",
                        'internationalMessages' => "$service->internationalMessages",
                        'carrierId' => "$service->carrierId",
                    ],
                ],
            ])
            ->seeJson([
                'status' => 'Enabled',
                'type' => 'services',
                'title' => 'title1',
                'planCode' => '11111',
                'cost' => '30',
                'description' => 'desc1',
                'domesticMinutes' => '100',
                'domesticData' => '100',
                'domesticMessages' => '100',
                'internationalMinutes' => '100',
                'internationalData' => '100',
                'internationalMessages' => '100',
                'carrierId' => "1",
            ]);
    }

    public function testUpdateServiceIncludeAddons(){

        $carrier = factory(\WA\DataStore\Carrier\Carrier::class)->create();

        $service = factory(\WA\DataStore\Service\Service::class)->create(['carrierId' => $carrier->id]);

        $addon1 = factory(\WA\DataStore\Addon\Addon::class)->create(['serviceId' => $service->id]);
        $addon2 = factory(\WA\DataStore\Addon\Addon::class)->create(['serviceId' => $service->id]);
        $addon3 = factory(\WA\DataStore\Addon\Addon::class)->create(['serviceId' => $service->id]);

/*
        $var = $this->get('/services/'.$service->id.'?include=addons')
            ->seeJsonStructure([                
                'data' => [
                    'type',
                    'id',
                    'attributes' => [
                        'status',
                        'title',
                        'planCode',
                        'cost',
                        'description',
                        'domesticMinutes',
                        'domesticData',
                        'domesticMessages',
                        'internationalMinutes',
                        'internationalData',
                        'internationalMessages',
                        'carrierId',
                        'created_at',
                        'updated_at',
                    ],
                    'links' => [
                        'self'
                    ],
                    'relationships' => [
                        'carriers' => [
                            'links' => [
                                'self',
                                'related',
                            ],
                            'data' => [
                                [
                                    'type',
                                    'id',
                                ]
                            ]
                        ],
                        'addons' => [
                            'links' => [
                                'self',
                                'related',
                            ],
                            'data' => [
                                [
                                    'type',
                                    'id',
                                ],
                                [
                                    'type',
                                    'id',
                                ],
                                [
                                    'type',
                                    'id',
                                ]
                            ]
                        ]
                    ]
                ],
                'included' => [
                    0 => [
                        'type',
                        'id',
                        'attributes' => [
                            'name',
                            'presentation',
                            'active',
                            'locationId',
                            'shortName',
                            'created_at',
                            'updated_at',
                        ],
                        'links' => [
                            'self'
                        ]
                    ],
                    1 => [
                        'type',
                        'id',
                        'attributes' => [
                            'name',
                            'cost',
                            'serviceId',
                            'created_at',
                            'updated_at',
                        ],
                        'links' => [
                            'self'
                        ]
                    ],
                    2 => [
                        'type',
                        'id',
                        'attributes' => [
                            'name',
                            'cost',
                            'serviceId',
                            'created_at',
                            'updated_at',
                        ],
                        'links' => [
                            'self'
                        ]
                    ],
                    3 => [
                        'type',
                        'id',
                        'attributes' => [
                            'name',
                            'cost',
                            'serviceId',
                            'created_at',
                            'updated_at',
                        ],
                        'links' => [
                            'self'
                        ]
                    ]    
                ]
            ]);
*/
        $res = $this->put('/services/'.$service->id.'?include=addons',
            [
                'data' => [
                    'type' => 'services',
                    'attributes' => [
                        'status' => 'Enabled',
                        'title' => 'title1',
                        'planCode' => '11111',
                        'cost' => '30',
                        'description' => 'desc1',
                        'domesticMinutes' => '100',
                        'domesticData' => '100',
                        'domesticMessages' => '100',
                        'internationalMinutes' => '100',
                        'internationalData' => '100',
                        'internationalMessages' => '100',
                        'carrierId' => '1',
                        'created_at' => $service->created_at,
                        'updated_at' => $service->updated_at
                    ],
                    'relationships' => [
                        'addons' => [
                            'data' => [
                                ['id' => $addon1->id, 'type' => 'addons', 'name' => $addon1->name, 'cost' => $addon1->cost, 'serviceId' => $service->id],
                                ['id' => $addon2->id, 'type' => 'addons', 'name' => $addon2->name, 'cost' => $addon2->cost, 'serviceId' => $service->id],
                                
                            ],
                        ],
                    ]
                ]
            ])->seeJsonStructure([
                'data' => [
                    'type',
                    'id',
                    'attributes' => [
                        'status',
                        'title',
                        'planCode',
                        'cost',
                        'description',
                        'domesticMinutes',
                        'domesticData',
                        'domesticMessages',
                        'internationalMinutes',
                        'internationalData',
                        'internationalMessages',
                        'carrierId',
                        'created_at',
                        'updated_at',
                    ],
                    'links' => [
                        'self'
                    ],
                    'relationships' => [
                        'carriers' => [
                            'links' => [
                                'self',
                                'related',
                            ],
                            'data' => [
                                [
                                    'type',
                                    'id',
                                ]
                            ]
                        ],
                        'addons' => [
                            'links' => [
                                'self',
                                'related',
                            ],
                            'data' => [
                                [
                                    'type',
                                    'id',
                                ],
                                [
                                    'type',
                                    'id',
                                ]
                            ]
                        ]
                    ]
                ],
                'included' => [
                    0 => [
                        'type',
                        'id',
                        'attributes' => [
                            'name',
                            'presentation',
                            'active',
                            'locationId',
                            'shortName',
                            'created_at',
                            'updated_at',
                        ],
                        'links' => [
                            'self'
                        ]
                    ],
                    1 => [
                        'type',
                        'id',
                        'attributes' => [
                            'name',
                            'cost',
                            'serviceId',
                            'created_at',
                            'updated_at',
                        ],
                        'links' => [
                            'self'
                        ]
                    ],
                    2 => [
                        'type',
                        'id',
                        'attributes' => [
                            'name',
                            'cost',
                            'serviceId',
                            'created_at',
                            'updated_at',
                        ],
                        'links' => [
                            'self'
                        ]
                    ]
                ]
            ]);
    }

    public function testDeleteServiceIfExists()
    {
        // CREATE & DELETE
        $service = factory(\WA\DataStore\Service\Service::class)->create();
        $responseDel = $this->call('DELETE', '/services/'.$service->id);
        $this->assertEquals(200, $responseDel->status());
        $responseGet = $this->call('GET', '/services/'.$service->id);
        $this->assertEquals(404, $responseGet->status());
    }

    public function testDeleteServiceIfNoExists()
    {
        // DELETE NO EXISTING.
        $responseDel = $this->call('DELETE', '/services/1');
        $this->assertEquals(404, $responseDel->status());
    }
}
