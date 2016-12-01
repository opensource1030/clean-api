<?php

use Laravel\Lumen\Testing\DatabaseMigrations;

class OrdersApiTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * A basic functional test for services.
     *
     * @AD: Some changes and a little modification in OrderController@index - Comment ApplyMeta()
     */
    public function testGetOrders()
    {
        factory(\WA\DataStore\Order\Order::class, 40)->create();

        $res = $this->json('GET', 'orders');

        $res->seeJsonStructure([
                'data' => [
                    0 => ['type', 'id',
                        'attributes' => [
                            'status',
                            'userId',
                            'packageId',
                            'deviceId',
                            'serviceId',
                            'carrierId',
                            'created_at',
                            'updated_at',
                        ],
                        'links',
                    ],
                ],
            ]);
    }

    public function testGetOrderById()
    {
        $order = factory(\WA\DataStore\Order\Order::class)->create();

        $res = $this->json('GET', 'orders/'.$order->id)
            ->seeJson([
                'type' => 'orders',
                'status' => $order->status,
                'userId' => $order->userId,
                'packageId' => $order->packageId,
                'deviceId' => $order->deviceId,
                'serviceId' => $order->serviceId,
            ])->seeJsonStructure([
                'data' => [
                    'type', 
                    'id',
                    'attributes' => [
                        'status',
                        'userId',
                        'packageId',
                        'deviceId',
                        'serviceId',
                        'carrierId'
                    ],
                    'links' => [
                        'self',
                    ]
                ],
                'meta' => [
                    'sort',
                    'filter',
                    'fields'
                ]
            ]);
    }

    public function testGetOrderByIdIfNoExists()
    {
        $orderId = factory(\WA\DataStore\Order\Order::class)->create()->id;
        $orderId = $orderId + 10;

        $response = $this->call('GET', '/orders/'.$orderId);
        $this->assertEquals(404, $response->status());
    }

    public function testGetOrderByIdIncludeAll()
    {
        $user = factory(\WA\DataStore\User\User::class)->create();
        $package = factory(\WA\DataStore\Package\Package::class)->create();
        $service = factory(\WA\DataStore\Service\Service::class)->create();
        $device = factory(\WA\DataStore\Device\Device::class)->create();
        $carrier = factory(\WA\DataStore\Carrier\Carrier::class)->create();

        $order = factory(\WA\DataStore\Order\Order::class)->create(['userId' => $user->id, 'packageId' => $package->id, 'serviceId' => $service->id, 'deviceId' => $device->id, 'carrierId' => $carrier->id]);

        $app1 = factory(\WA\DataStore\App\App::class)->create()->id;
        $app2 = factory(\WA\DataStore\App\App::class)->create()->id;
        $dataApps = array($app1, $app2);
        $order->apps()->sync($dataApps);

        $serviceitem1 = factory(\WA\DataStore\ServiceItem\ServiceItem::class)->create()->id;
        $serviceitem2 = factory(\WA\DataStore\ServiceItem\ServiceItem::class)->create()->id;
        $dataServiceItems = array($serviceitem1, $serviceitem2);
        $order->serviceitems()->sync($dataServiceItems);

        $res = $this->json('GET', '/orders/'.$order->id.'?include=users,packages,services,devices,carriers,apps,serviceitems')
        //Log::debug("testGetOrderByIdIncludeAll: ".print_r($res->response->getContent(), true));
            ->seeJsonStructure([
                'data' => [
                    'type',
                    'id',
                    'attributes' => [
                        'status',
                        'userId',
                        'packageId',
                        'deviceId',
                        'serviceId',
                        'carrierId'
                    ],
                    'links' => [
                        'self',
                    ],
                    'relationships' => [
                        'users' => [
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
                        'packages' => [
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
                        'devices' => [
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
                        'services' => [
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
                        'carriers' => [
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
                        'apps' => [
                            'links' => [
                                'self',
                                'related',
                            ],
                            'data' => [
                                0 => [
                                    'type',
                                    'id',
                                ],
                                1 => [
                                    'type',
                                    'id',
                                ]
                            ],
                        ],
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
                                1 => [
                                    'type',
                                    'id',
                                ]
                            ],
                        ]
                    ]
                ],
            //]);
                'included' => [
                    0 => [
                        'type',
                        'id',
                        'attributes' => [
                            'make',
                            'model',
                            'class',
                            'deviceOS',
                            'description',
                            'statusId',
                            'image'
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
                            'presentation',
                            'active',
                            'locationId',
                            'shortName',
                            'created_at',
                            'updated_at'
                        ],
                        'links' => [
                            'self'
                        ]
                    ],
                    2 => [
                        'type',
                        'id',
                        'attributes' => [
                            'identification',
                            'name',
                            'properties',
                            'externalId',
                            'statusId',
                            'syncId',
                            'created_at',
                            'updated_at'
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
                            'addressId',
                            'companyId',
                            'created_at',
                            'updated_at'
                        ],
                        'links' => [
                            'self'
                        ]
                    ],
                    4 => [
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
                            'updated_at'
                        ],
                        'links' => [
                            'self'
                        ]
                    ],
                    5 => [
                        'type',
                        'id',
                        'attributes' => [
                            'type',
                            'image',
                            'description',
                            'created_at',
                            'updated_at'
                        ],
                        'links' => [
                            'self'
                        ]
                    ],
                    6 => [
                        'type',
                        'id',
                        'attributes' => [
                            'type',
                            'image',
                            'description',
                            'created_at',
                            'updated_at'
                        ],
                        'links' => [
                            'self'
                        ]
                    ],
                    7 => [
                        'type',
                        'id',
                        'attributes' => [
                            'serviceId',
                            'category',
                            'description',
                            'value',
                            'unit',
                            'cost',
                            'domain'
                        ],
                        'links' => [
                            'self'
                        ]
                    ],
                    8 => [
                        'type',
                        'id',
                        'attributes' => [
                            'serviceId',
                            'category',
                            'description',
                            'value',
                            'unit',
                            'cost',
                            'domain'
                        ],
                        'links' => [
                            'self'
                        ]
                    ]
                ]
            ]);
    }

    public function testCreateOrder()
    {
        $userId = factory(\WA\DataStore\User\User::class)->create()->id;
        $packageId = factory(\WA\DataStore\Package\Package::class)->create()->id;
        $deviceId = factory(\WA\DataStore\Device\Device::class)->create()->id;
        $serviceId = factory(\WA\DataStore\Service\Service::class)->create()->id;
        $carrierId = factory(\WA\DataStore\Carrier\Carrier::class)->create()->id;

        $app1 = factory(\WA\DataStore\App\App::class)->create()->id;
        $app2 = factory(\WA\DataStore\App\App::class)->create()->id;

        $serviceitem1 = factory(\WA\DataStore\ServiceItem\ServiceItem::class)->create()->id;
        $serviceitem2 = factory(\WA\DataStore\ServiceItem\ServiceItem::class)->create()->id;

        $res = $this->json('POST', '/orders?include=users,packages,services,devices,carriers,apps,serviceitems',
            [
                'data' => [
                    'type' => 'orders',
                    'attributes' => [
                        'status' => 'Enabled',
                        'userId' => $userId,
                        'packageId' => $packageId,
                        'deviceId' => $deviceId,
                        'serviceId' => $serviceId,
                        'carrierId' => $carrierId
                    ],
                    'relationships' => [
                        'apps' => [
                            'data' => [
                                ['type' => 'apps', 'id' => $app1],
                                ['type' => 'apps', 'id' => $app2],
                            ],
                        ],
                        'serviceitems' => [
                            'data' => [
                                ['type' => 'serviceitems', 'id' => $serviceitem1],
                                ['type' => 'serviceitems', 'id' => $serviceitem2],
                            ],
                        ]
                    ]
                ]
            ]
            )
            //Log::debug("testCreateUser: ".print_r($res->response->getContent(), true));
            ->seeJson(
                [
                    'status' => 'Enabled',
                    'userId' => $userId,
                    'packageId' => $packageId,
                    'deviceId' => $deviceId,
                    'serviceId' => $serviceId,
                    'carrierId' => $carrierId
                ])
            ->seeJsonStructure(
                [
                    'data' => [
                        'type',
                        'id',
                        'attributes' => [
                            'status',
                            'userId',
                            'packageId',
                            'deviceId',
                            'serviceId',
                            'carrierId'
                        ],
                        'links' => [
                            'self'
                        ],
                        'relationships' => [
                            'users' => [
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
                            'packages' => [
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
                            'devices' => [
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
                            'services' => [
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
                            'carriers' => [
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
                            'apps' => [
                                'links' => [
                                    'self',
                                    'related',
                                ],
                                'data' => [
                                    0 => [
                                        'type',
                                        'id',
                                    ],
                                    1 => [
                                        'type',
                                        'id',
                                    ]
                                ],
                            ],
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
                                    1 => [
                                        'type',
                                        'id',
                                    ]
                                ],
                            ]
                        ]
                    ],
                    'included' => [
                        0 => [
                            'type',
                            'id',
                            'attributes' => [
                                'make',
                                'model',
                                'class',
                                'deviceOS',
                                'description',
                                'statusId',
                                'image'
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
                                'presentation',
                                'active',
                                'locationId',
                                'shortName',
                                'created_at',
                                'updated_at'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        2 => [
                            'type',
                            'id',
                            'attributes' => [
                                'identification',
                                'name',
                                'properties',
                                'externalId',
                                'statusId',
                                'syncId',
                                'created_at',
                                'updated_at'
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
                                'addressId',
                                'companyId',
                                'created_at',
                                'updated_at'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        4 => [
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
                                'updated_at'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        5 => [
                            'type',
                            'id',
                            'attributes' => [
                                'type',
                                'image',
                                'description',
                                'created_at',
                                'updated_at'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        6 => [
                            'type',
                            'id',
                            'attributes' => [
                                'type',
                                'image',
                                'description',
                                'created_at',
                                'updated_at'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        7 => [
                            'type',
                            'id',
                            'attributes' => [
                                'serviceId',
                                'category',
                                'description',
                                'value',
                                'unit',
                                'cost',
                                'domain'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        8 => [
                            'type',
                            'id',
                            'attributes' => [
                                'serviceId',
                                'category',
                                'description',
                                'value',
                                'unit',
                                'cost',
                                'domain'
                            ],
                            'links' => [
                                'self'
                            ]
                        ]
                    ]
                ]);
    }

    public function testUpdateOrder()
    {
        $order1 = factory(\WA\DataStore\Order\Order::class)->create();
        $order2 = factory(\WA\DataStore\Order\Order::class)->create();

        $this->assertNotEquals($order1->id, $order2->id);
        $this->assertNotEquals($order1->status, $order2->status);
        $this->assertNotEquals($order1->userId, $order2->userId);
        $this->assertNotEquals($order1->packageId, $order2->packageId);
        $this->assertNotEquals($order1->deviceId, $order2->deviceId);
        $this->assertNotEquals($order1->serviceId, $order2->serviceId);

        $this->PATCH('/orders/'.$order1->id,
            [
                'data' => [
                    'type' => 'orders',
                    'attributes' => [
                        'status' => $order2->status,
                        'userId' => $order2->userId,
                        'packageId' => $order2->packageId,
                        'deviceId' => $order2->deviceId,
                        'serviceId' => $order2->serviceId,
                    ],
                ],
            ])
            ->seeJson([
                'type' => 'orders',
                'id' => "$order1->id",
                'status' => $order2->status,
                'userId' => $order2->userId,
                'packageId' => $order2->packageId,
                'deviceId' => $order2->deviceId,
                'serviceId' => $order2->serviceId,
            ]);
    }
    
    public function testUpdateUserDeleteRelationships()
    {
        $userId = factory(\WA\DataStore\User\User::class)->create()->id;
        $packageId = factory(\WA\DataStore\Package\Package::class)->create()->id;
        $deviceId = factory(\WA\DataStore\Device\Device::class)->create()->id;
        $serviceId = factory(\WA\DataStore\Service\Service::class)->create()->id;
        $carrierId = factory(\WA\DataStore\Carrier\Carrier::class)->create()->id;

        $order = factory(\WA\DataStore\Order\Order::class)->create();

        $app1 = factory(\WA\DataStore\App\App::class)->create()->id;
        $app2 = factory(\WA\DataStore\App\App::class)->create()->id;
        $order->apps()->sync(array($app1, $app2));

        $orderAppDB = DB::table('order_apps')->where('orderId', $order->id)->get();
        $this->assertCount(2, $orderAppDB);
        $this->assertEquals($orderAppDB[0]->appId, $app1);
        $this->assertEquals($orderAppDB[1]->appId, $app2);

        $serviceitem1 = factory(\WA\DataStore\ServiceItem\ServiceItem::class)->create()->id;
        $serviceitem2 = factory(\WA\DataStore\ServiceItem\ServiceItem::class)->create()->id;
        $order->serviceitems()->sync(array($serviceitem1, $serviceitem2));

        $orderSIDB = DB::table('order_serviceitems')->where('orderId', $order->id)->get();
        $this->assertCount(2, $orderSIDB);
        $this->assertEquals($orderSIDB[0]->serviceItemId, $serviceitem1);
        $this->assertEquals($orderSIDB[1]->serviceItemId, $serviceitem2);

        $res = $this->json('PATCH', '/orders/'.$order->id.'?include=users,packages,services,devices,carriers,apps,serviceitems',
            [
                'data' => [
                    'type' => 'orders',
                    'attributes' => [
                        'status' => 'Enabled',
                        'userId' => $userId,
                        'packageId' => $packageId,
                        'deviceId' => $deviceId,
                        'serviceId' => $serviceId,
                        'carrierId' => $carrierId
                    ],
                    'relationships' => [
                        'apps' => [
                            'data' => [
                                ['type' => 'apps', 'id' => $app1]
                            ],
                        ],
                        'serviceitems' => [
                            'data' => [
                                ['type' => 'serviceitems', 'id' => $serviceitem1]
                            ],
                        ]
                    ]
                ]
            ]
            )
            //Log::debug("RES TEST: ".print_r($res->response->getContent(), true));
            ->seeJson(
                [
                    'status' => 'Enabled',
                    'userId' => $userId,
                    'packageId' => $packageId,
                    'deviceId' => $deviceId,
                    'serviceId' => $serviceId,
                    'carrierId' => $carrierId
                ])
            ->seeJsonStructure(
                [
                    'data' => [
                        'type',
                        'id',
                        'attributes' => [
                            'status',
                            'userId',
                            'packageId',
                            'deviceId',
                            'serviceId',
                            'carrierId'
                        ],
                        'links' => [
                            'self'
                        ],
                        'relationships' => [
                            'users' => [
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
                            'packages' => [
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
                            'devices' => [
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
                            'services' => [
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
                            'carriers' => [
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
                            'apps' => [
                                'links' => [
                                    'self',
                                    'related',
                                ],
                                'data' => [
                                    0 => [
                                        'type',
                                        'id',
                                    ]
                                ],
                            ],
                            'serviceitems' => [
                                'links' => [
                                    'self',
                                    'related',
                                ],
                                'data' => [
                                    0 => [
                                        'type',
                                        'id',
                                    ]
                                ],
                            ]
                        ]
                    ],
                    'included' => [
                        0 => [
                            'type',
                            'id',
                            'attributes' => [
                                'make',
                                'model',
                                'class',
                                'deviceOS',
                                'description',
                                'statusId',
                                'image'
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
                                'presentation',
                                'active',
                                'locationId',
                                'shortName',
                                'created_at',
                                'updated_at'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        2 => [
                            'type',
                            'id',
                            'attributes' => [
                                'identification',
                                'name',
                                'properties',
                                'externalId',
                                'statusId',
                                'syncId',
                                'created_at',
                                'updated_at'
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
                                'addressId',
                                'companyId',
                                'created_at',
                                'updated_at'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        4 => [
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
                                'updated_at'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        5 => [
                            'type',
                            'id',
                            'attributes' => [
                                'type',
                                'image',
                                'description',
                                'created_at',
                                'updated_at'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        6 => [
                            'type',
                            'id',
                            'attributes' => [
                                'serviceId',
                                'category',
                                'description',
                                'value',
                                'unit',
                                'cost',
                                'domain'
                            ],
                            'links' => [
                                'self'
                            ]
                        ]
                    ]
                ]);
    }

    public function testUpdateUserAddRelationships()
    {
        $userId = factory(\WA\DataStore\User\User::class)->create()->id;
        $packageId = factory(\WA\DataStore\Package\Package::class)->create()->id;
        $deviceId = factory(\WA\DataStore\Device\Device::class)->create()->id;
        $serviceId = factory(\WA\DataStore\Service\Service::class)->create()->id;
        $carrierId = factory(\WA\DataStore\Carrier\Carrier::class)->create()->id;

        $order = factory(\WA\DataStore\Order\Order::class)->create();

        $app1 = factory(\WA\DataStore\App\App::class)->create()->id;
        $app2 = factory(\WA\DataStore\App\App::class)->create()->id;
        $order->apps()->sync(array($app1, $app2));

        $orderAppDB = DB::table('order_apps')->where('orderId', $order->id)->get();
        $this->assertCount(2, $orderAppDB);
        $this->assertEquals($orderAppDB[0]->appId, $app1);
        $this->assertEquals($orderAppDB[1]->appId, $app2);

        $app3 = factory(\WA\DataStore\App\App::class)->create()->id;

        $serviceitem1 = factory(\WA\DataStore\ServiceItem\ServiceItem::class)->create()->id;
        $serviceitem2 = factory(\WA\DataStore\ServiceItem\ServiceItem::class)->create()->id;
        $order->serviceitems()->sync(array($serviceitem1, $serviceitem2));

        $orderSIDB = DB::table('order_serviceitems')->where('orderId', $order->id)->get();
        $this->assertCount(2, $orderSIDB);
        $this->assertEquals($orderSIDB[0]->serviceItemId, $serviceitem1);
        $this->assertEquals($orderSIDB[1]->serviceItemId, $serviceitem2);

        $serviceitem3 = factory(\WA\DataStore\ServiceItem\ServiceItem::class)->create()->id;

        $res = $this->json('PATCH', '/orders/'.$order->id.'?include=users,packages,services,devices,carriers,apps,serviceitems',
            [
                'data' => [
                    'type' => 'orders',
                    'attributes' => [
                        'status' => 'Enabled',
                        'userId' => $userId,
                        'packageId' => $packageId,
                        'deviceId' => $deviceId,
                        'serviceId' => $serviceId,
                        'carrierId' => $carrierId
                    ],
                    'relationships' => [
                        'apps' => [
                            'data' => [
                                ['type' => 'apps', 'id' => $app1],
                                ['type' => 'apps', 'id' => $app2],
                                ['type' => 'apps', 'id' => $app3]
                            ],
                        ],
                        'serviceitems' => [
                            'data' => [
                                ['type' => 'serviceitems', 'id' => $serviceitem1],
                                ['type' => 'serviceitems', 'id' => $serviceitem2],
                                ['type' => 'serviceitems', 'id' => $serviceitem3]
                            ],
                        ]
                    ]
                ]
            ]
            )
            //Log::debug("RES TEST: ".print_r($res->response->getContent(), true));
            ->seeJson(
                [
                    'status' => 'Enabled',
                    'userId' => $userId,
                    'packageId' => $packageId,
                    'deviceId' => $deviceId,
                    'serviceId' => $serviceId,
                    'carrierId' => $carrierId
                ])
            ->seeJsonStructure(
                [
                    'data' => [
                        'type',
                        'id',
                        'attributes' => [
                            'status',
                            'userId',
                            'packageId',
                            'deviceId',
                            'serviceId',
                            'carrierId'
                        ],
                        'links' => [
                            'self'
                        ],
                        'relationships' => [
                            'users' => [
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
                            'packages' => [
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
                            'devices' => [
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
                            'services' => [
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
                            'carriers' => [
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
                            'apps' => [
                                'links' => [
                                    'self',
                                    'related',
                                ],
                                'data' => [
                                    0 => [
                                        'type',
                                        'id',
                                    ],
                                    1 => [
                                        'type',
                                        'id',
                                    ],
                                    2 => [
                                        'type',
                                        'id',
                                    ]
                                ],
                            ],
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
                                    1 => [
                                        'type',
                                        'id',
                                    ],
                                    2 => [
                                        'type',
                                        'id',
                                    ]
                                ],
                            ]
                        ]
                    ],
                    'included' => [
                        0 => [
                            'type',
                            'id',
                            'attributes' => [
                                'make',
                                'model',
                                'class',
                                'deviceOS',
                                'description',
                                'statusId',
                                'image'
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
                                'presentation',
                                'active',
                                'locationId',
                                'shortName',
                                'created_at',
                                'updated_at'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        2 => [
                            'type',
                            'id',
                            'attributes' => [
                                'identification',
                                'name',
                                'properties',
                                'externalId',
                                'statusId',
                                'syncId',
                                'created_at',
                                'updated_at'
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
                                'addressId',
                                'companyId',
                                'created_at',
                                'updated_at'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        4 => [
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
                                'updated_at'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        5 => [
                            'type',
                            'id',
                            'attributes' => [
                                'type',
                                'image',
                                'description',
                                'created_at',
                                'updated_at'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        6 => [
                            'type',
                            'id',
                            'attributes' => [
                                'type',
                                'image',
                                'description',
                                'created_at',
                                'updated_at'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        7 => [
                            'type',
                            'id',
                            'attributes' => [
                                'type',
                                'image',
                                'description',
                                'created_at',
                                'updated_at'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        8 => [
                            'type',
                            'id',
                            'attributes' => [
                                'serviceId',
                                'category',
                                'description',
                                'value',
                                'unit',
                                'cost',
                                'domain'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        9 => [
                            'type',
                            'id',
                            'attributes' => [
                                'serviceId',
                                'category',
                                'description',
                                'value',
                                'unit',
                                'cost',
                                'domain'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        10 => [
                            'type',
                            'id',
                            'attributes' => [
                                'serviceId',
                                'category',
                                'description',
                                'value',
                                'unit',
                                'cost',
                                'domain'
                            ],
                            'links' => [
                                'self'
                            ]
                        ]
                    ]
                ]);
    }

    public function testDeleteOrderIfExists()
    {
        // CREATE & DELETE
        $order = factory(\WA\DataStore\Order\Order::class)->create();
        $responseDel = $this->call('DELETE', '/orders/'.$order->id);
        $this->assertEquals(200, $responseDel->status());
        $responseGet = $this->call('GET', '/orders/'.$order->id);
        $this->assertEquals(404, $responseGet->status());
    }

    public function testDeleteOrderIfNoExists()
    {
        // DELETE NO EXISTING.
        $responseDel = $this->call('DELETE', '/orders/1');
        $this->assertEquals(404, $responseDel->status());
    }
}
