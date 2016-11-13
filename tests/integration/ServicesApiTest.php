<?php

//use Laravel\Lumen\Testing\DatabaseTransactions;
use Laravel\Lumen\Testing\DatabaseMigrations;

use WA\DataStore\ServiceItem\ServiceItem;



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

        $res = $this->get('services')->seeJsonStructure([
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
                'carrierId' => "$service->carrierId",
            ]);
        
    }

    public function testGetServiceByIdandIncludesserviceitems()
    {
        $service = factory(\WA\DataStore\Service\Service::class)->create();

        factory(\WA\DataStore\ServiceItem\ServiceItem::class)->create(['serviceId' => $service->id]);
        factory(\WA\DataStore\ServiceItem\ServiceItem::class)->create(['serviceId' => $service->id]);

        $response = $this->json('GET', 'services/'.$service->id.'?include=serviceitems')
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
                        'serviceitems' => [
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
                            'category',
                            'description',
                            'value',
                            'unit',
                            'cost',
                            'domain',
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
                
                'carrierId' => "1",
            ]);
    }

    public function testUpdateService()
    {
        $service = factory(\WA\DataStore\Service\Service::class)->create(
            ['status' => 'Enabled', 'title' => 'title1', 'planCode' => 11111, 'cost' => 30, 'description' => 'desc1', 'carrierId' => "1"]
        );
        $serviceAux = factory(\WA\DataStore\Service\Service::class)->create(
            ['status' => 'Disabled', 'title' => 'title2', 'planCode' => 22222, 'cost' => 40, 'description' => 'desc2', 'carrierId' => "2"]
        );

        $this->assertNotEquals($service->status, $serviceAux->status);
        $this->assertNotEquals($service->id, $serviceAux->id);
        $this->assertNotEquals($service->title, $serviceAux->title);
        $this->assertNotEquals($service->cost, $serviceAux->cost);
        $this->assertNotEquals($service->description, $serviceAux->description);
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
                
                'carrierId' => "1",
            ]);
    }

    public function testUpdateServiceIncludeserviceitems(){

        $carrier = factory(\WA\DataStore\Carrier\Carrier::class)->create();

        $service = factory(\WA\DataStore\Service\Service::class)->create(['carrierId' => $carrier->id]);

        $serviceitem1 = factory(\WA\DataStore\ServiceItem\ServiceItem::class)->create(['serviceId' => $service->id]);
        $serviceitem2 = factory(\WA\DataStore\ServiceItem\ServiceItem::class)->create(['serviceId' => $service->id]);
        $serviceitem3 = factory(\WA\DataStore\ServiceItem\ServiceItem::class)->create(['serviceId' => $service->id]);

/*
        $var = $this->get('/services/'.$service->id.'?include=serviceitems')
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
                        'serviceitems' => [
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
        $res = $this->put('/services/'.$service->id.'?include=serviceitems',
            [
                'data' => [
                    'type' => 'services',
                    'attributes' => [
                        'status' => 'Enabled',
                        'title' => 'title1',
                        'planCode' => '11111',
                        'cost' => '30',
                        'description' => 'desc1',                        
                        'carrierId' => '1',
                        'created_at' => $service->created_at,
                        'updated_at' => $service->updated_at
                    ],
                    'relationships' => [
                        'serviceitems' => [
                            'data' => [
                                [
                                    'id'            => $serviceitem1->id,
                                    'type'          => 'serviceitems',
                                    'name'          => $serviceitem1->name,
                                    'category'      => $serviceitem1->category,
                                    'description'   => $serviceitem1->description,
                                    'value'         => $serviceitem1->value,
                                    'unit'          => $serviceitem1->unit,
                                    'cost'          => $serviceitem1->cost,
                                    'domain'        => $serviceitem1->domain,
                                ],
                                [
                                    'id'            => $serviceitem2->id,
                                    'type'          => 'serviceitems',
                                    'name'          => $serviceitem2->name,
                                    'category'      => $serviceitem2->category,
                                    'description'   => $serviceitem2->description,
                                    'value'         => $serviceitem2->value,
                                    'unit'          => $serviceitem2->unit,
                                    'cost'          => $serviceitem2->cost,
                                    'domain'        => $serviceitem2->domain,
                                ]
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
                        'serviceitems' => [
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
                            'category',
                            'description',
                            'value',
                            'unit',
                            'cost',
                            'domain',
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
                            'category',
                            'description',
                            'value',
                            'unit',
                            'cost',
                            'domain',
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
