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
                'serviceId' => $order->serviceId,
            ])->seeJsonStructure([
                'data' => [
                    'type', 
                    'id',
                    'attributes' => [
                        'status',
                        'userId',
                        'packageId',
                        'serviceId',
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
        $order = factory(\WA\DataStore\Order\Order::class)->create(['userId' => $user->id, 'packageId' => $package->id, 'serviceId' => $service->id]);

        $app1 = factory(\WA\DataStore\App\App::class)->create()->id;
        $app2 = factory(\WA\DataStore\App\App::class)->create()->id;
        $dataApps = array($app1, $app2);
        $order->apps()->sync($dataApps);

        $deviceVariation1 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create()->id;
        $deviceVariation2 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create()->id;
        $dataDeviceVariations = array($deviceVariation1, $deviceVariation2);
        $order->deviceVariations()->sync($dataDeviceVariations);

        $res = $this->json('GET', '/orders/'.$order->id.'?include=users,packages,services,devicevariations,apps')
        //Log::debug("testGetOrderByIdIncludeAll: ".print_r($res->response->getContent(), true));
            ->seeJsonStructure([
                'data' => [
                    'type',
                    'id',
                    'attributes' => [
                        'status',
                        'userId',
                        'packageId',
                        'serviceId',
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
                        'devicevariations' => [
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
                        ]
                    ]
                ],
            //]);
                'included' => [
                    0 => [ // USERS
                        'type',
                        'id',
                        'attributes' => [
                            'uuid',
                            'identification',
                            'email',
                            'alternateEmail',
                            'username',
                            'syncId'
                        ],
                        'links' => [
                            'self'
                        ]
                    ],
                    1 => [ // PACKAGE
                        'type',
                        'id',
                        'attributes' => [
                            'name',
                            'addressId',
                            'companyId'
                        ],
                        'links' => [
                            'self'
                        ]
                    ],
                    2 => [ // SERVICE
                        'type',
                        'id',
                        'attributes' => [
                            'status',
                            'title',
                            'planCode',
                            'cost',
                            'description',
                            'carrierId'
                        ],
                        'links' => [
                            'self'
                        ]
                    ],
                    3 => [ // APP
                        'type',
                        'id',
                        'attributes' => [
                            'type',
                            'image',
                            'description'
                        ],
                        'links' => [
                            'self'
                        ]
                    ],
                    4 => [ // APP
                        'type',
                        'id',
                        'attributes' => [
                            'type',
                            'image',
                            'description'
                        ],
                        'links' => [
                            'self'
                        ]
                    ],
                    5 => [ // DEVICEVARIATIONS
                        'type',
                        'id',
                        'attributes' => [
                            'priceRetail',
                            'price1',
                            'price2',
                            'priceOwn',
                            'deviceId',
                            'carrierId',
                            'companyId'
                        ],
                        'links' => [
                            'self'
                        ]
                    ],
                    6 => [ // DEVICEVARIATIONS
                        'type',
                        'id',
                        'attributes' => [
                            'priceRetail',
                            'price1',
                            'price2',
                            'priceOwn',
                            'deviceId',
                            'carrierId',
                            'companyId'
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
        $serviceId = factory(\WA\DataStore\Service\Service::class)->create()->id;

        $app1 = factory(\WA\DataStore\App\App::class)->create()->id;
        $app2 = factory(\WA\DataStore\App\App::class)->create()->id;

        $serviceitem1 = factory(\WA\DataStore\ServiceItem\ServiceItem::class)->create()->id;
        $serviceitem2 = factory(\WA\DataStore\ServiceItem\ServiceItem::class)->create()->id;

        $deviceVariation1 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create()->id;
        $deviceVariation2 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create()->id;

        $res = $this->json('POST', '/orders?include=users,packages,services,devicevariations,apps',
            [
                'data' => [
                    'type' => 'orders',
                    'attributes' => [
                        'status' => 'Enabled',
                        'userId' => $userId,
                        'packageId' => $packageId,
                        'serviceId' => $serviceId
                    ],
                    'relationships' => [
                        'apps' => [
                            'data' => [
                                ['type' => 'apps', 'id' => $app1],
                                ['type' => 'apps', 'id' => $app2],
                            ],
                        ],
                        'devicevariations' => [
                            'data' => [
                                ['type' => 'devicevariations', 'id' => $deviceVariation1],
                                ['type' => 'devicevariations', 'id' => $deviceVariation2],
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
                    'serviceId' => $serviceId
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
                            'serviceId'
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
                            'devicevariations' => [
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
                            ]
                        ]
                    ],
                    'included' => [
                        0 => [ // USER
                            'type',
                            'id',
                            'attributes' => [
                                'uuid',
                                'identification',
                                'email',
                                'alternateEmail',
                                'username',
                                'syncId'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        1 => [ // PACKAGE
                            'type',
                            'id',
                            'attributes' => [
                                'name',
                                'addressId',
                                'companyId'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        2 => [ // SERVICE
                            'type',
                            'id',
                            'attributes' => [
                                'status',
                                'title',
                                'planCode',
                                'cost',
                                'description',
                                'carrierId'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        3 => [ // APP
                            'type',
                            'id',
                            'attributes' => [
                                'type',
                                'image',
                                'description'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        4 => [ // APP
                            'type',
                            'id',
                            'attributes' => [
                                'type',
                                'image',
                                'description'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        5 => [ // DEVICEVARIATIONS
                            'type',
                            'id',
                            'attributes' => [
                                'priceRetail',
                                'price1',
                                'price2',
                                'priceOwn',
                                'deviceId',
                                'carrierId',
                                'companyId'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        6 => [ // DEVICEVARIATIONS
                            'type',
                            'id',
                            'attributes' => [
                                'priceRetail',
                                'price1',
                                'price2',
                                'priceOwn',
                                'deviceId',
                                'carrierId',
                                'companyId'
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
        $this->assertNotEquals($order1->serviceId, $order2->serviceId);

        $this->PATCH('/orders/'.$order1->id,
            [
                'data' => [
                    'type' => 'orders',
                    'attributes' => [
                        'status' => $order2->status,
                        'userId' => $order2->userId,
                        'packageId' => $order2->packageId,
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
                'serviceId' => $order2->serviceId,
            ]);
    }
    
    public function testUpdateUserDeleteRelationships()
    {
        $userId = factory(\WA\DataStore\User\User::class)->create()->id;
        $packageId = factory(\WA\DataStore\Package\Package::class)->create()->id;
        $serviceId = factory(\WA\DataStore\Service\Service::class)->create()->id;

        $order = factory(\WA\DataStore\Order\Order::class)->create();

        $app1 = factory(\WA\DataStore\App\App::class)->create()->id;
        $app2 = factory(\WA\DataStore\App\App::class)->create()->id;
        $order->apps()->sync(array($app1, $app2));

        $orderAppDB = DB::table('order_apps')->where('orderId', $order->id)->get();
        $this->assertCount(2, $orderAppDB);
        $this->assertEquals($orderAppDB[0]->appId, $app1);
        $this->assertEquals($orderAppDB[1]->appId, $app2);

        $deviceVariation1 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create()->id;
        $deviceVariation2 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create()->id;
        $order->deviceVariations()->sync(array($deviceVariation1, $deviceVariation2));

        $orderDDB = DB::table('order_device_variations')->where('orderId', $order->id)->get();
        $this->assertCount(2, $orderDDB);
        $this->assertEquals($orderDDB[0]->deviceVariationId, $deviceVariation1);
        $this->assertEquals($orderDDB[1]->deviceVariationId, $deviceVariation2);

        $res = $this->json('PATCH', '/orders/'.$order->id.'?include=users,packages,services,devicevariations,apps',
            [
                'data' => [
                    'type' => 'orders',
                    'attributes' => [
                        'status' => 'Enabled',
                        'userId' => $userId,
                        'packageId' => $packageId,
                        'serviceId' => $serviceId
                    ],
                    'relationships' => [
                        'apps' => [
                            'data' => [
                                ['type' => 'apps', 'id' => $app1]
                            ],
                        ],
                        'devicevariations' => [
                            'data' => [
                                ['type' => 'devicevariations', 'id' => $deviceVariation1]
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
                    'serviceId' => $serviceId
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
                            'serviceId'
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
                            'devicevariations' => [
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
                            ]
                        ]
                    ],
                    'included' => [
                        0 => [ // USER
                            'type',
                            'id',
                            'attributes' => [
                                'uuid',
                                'identification',
                                'email',
                                'alternateEmail',
                                'username',
                                'syncId'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        1 => [ // PACKAGE
                            'type',
                            'id',
                            'attributes' => [
                                'name',
                                'addressId',
                                'companyId'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        2 => [ // SERVICE
                            'type',
                            'id',
                            'attributes' => [
                                'status',
                                'title',
                                'planCode',
                                'cost',
                                'description',
                                'carrierId'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        3 => [ // APP
                            'type',
                            'id',
                            'attributes' => [
                                'type',
                                'image',
                                'description'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        4 => [ // DEVICEVARIATIONS
                            'type',
                            'id',
                            'attributes' => [
                                'priceRetail',
                                'price1',
                                'price2',
                                'priceOwn',
                                'deviceId',
                                'carrierId',
                                'companyId'
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

        $order = factory(\WA\DataStore\Order\Order::class)->create();

        $app1 = factory(\WA\DataStore\App\App::class)->create()->id;
        $app2 = factory(\WA\DataStore\App\App::class)->create()->id;
        $order->apps()->sync(array($app1, $app2));

        $orderAppDB = DB::table('order_apps')->where('orderId', $order->id)->get();
        $this->assertCount(2, $orderAppDB);
        $this->assertEquals($orderAppDB[0]->appId, $app1);
        $this->assertEquals($orderAppDB[1]->appId, $app2);

        $app3 = factory(\WA\DataStore\App\App::class)->create()->id;

        $deviceVariation1 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create()->id;
        $deviceVariation2 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create()->id;
        $order->deviceVariations()->sync(array($deviceVariation1, $deviceVariation2));

        $orderDDB = DB::table('order_device_variations')->where('orderId', $order->id)->get();
        $this->assertCount(2, $orderDDB);
        $this->assertEquals($orderDDB[0]->deviceVariationId, $deviceVariation1);
        $this->assertEquals($orderDDB[1]->deviceVariationId, $deviceVariation2);

        $deviceVariation3 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create()->id;

        $res = $this->json('PATCH', '/orders/'.$order->id.'?include=users,packages,services,devicevariations,apps',
            [
                'data' => [
                    'type' => 'orders',
                    'attributes' => [
                        'status' => 'Enabled',
                        'userId' => $userId,
                        'packageId' => $packageId,
                        'serviceId' => $serviceId
                    ],
                    'relationships' => [
                        'apps' => [
                            'data' => [
                                ['type' => 'apps', 'id' => $app1],
                                ['type' => 'apps', 'id' => $app2],
                                ['type' => 'apps', 'id' => $app3]
                            ]
                        ],
                        'devicevariations' => [
                            'data' => [
                                ['type' => 'devicevariations', 'id' => $deviceVariation1],
                                ['type' => 'devicevariations', 'id' => $deviceVariation2],
                                ['type' => 'devicevariations', 'id' => $deviceVariation3]
                            ]
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
                    'serviceId' => $serviceId
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
                            'serviceId'
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
                            'devicevariations' => [
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
                            ]
                        ]
                    ],
                    'included' => [
                        0 => [ // USER
                            'type',
                            'id',
                            'attributes' => [
                                'uuid',
                                'identification',
                                'email',
                                'alternateEmail',
                                'username',
                                'syncId'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        1 => [ // PACKAGE
                            'type',
                            'id',
                            'attributes' => [
                                'name',
                                'addressId',
                                'companyId'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        2 => [ // SERVICE
                            'type',
                            'id',
                            'attributes' => [
                                'status',
                                'title',
                                'planCode',
                                'cost',
                                'description',
                                'carrierId'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        3 => [ // APP
                            'type',
                            'id',
                            'attributes' => [
                                'type',
                                'image',
                                'description'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        4 => [ // APP
                            'type',
                            'id',
                            'attributes' => [
                                'type',
                                'image',
                                'description'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        5 => [ // APP
                            'type',
                            'id',
                            'attributes' => [
                                'type',
                                'image',
                                'description'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        6 => [ // DEVICEVARIATIONS
                            'type',
                            'id',
                            'attributes' => [
                                'priceRetail',
                                'price1',
                                'price2',
                                'priceOwn',
                                'deviceId',
                                'carrierId',
                                'companyId'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        7 => [ // DEVICEVARIATIONS
                            'type',
                            'id',
                            'attributes' => [
                                'priceRetail',
                                'price1',
                                'price2',
                                'priceOwn',
                                'deviceId',
                                'carrierId',
                                'companyId'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        8 => [ // DEVICEVARIATIONS
                            'type',
                            'id',
                            'attributes' => [
                                'priceRetail',
                                'price1',
                                'price2',
                                'priceOwn',
                                'deviceId',
                                'carrierId',
                                'companyId'
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
