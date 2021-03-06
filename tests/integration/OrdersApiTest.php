<?php



class OrdersApiTest extends \TestCase
{
    use \Laravel\Lumen\Testing\DatabaseMigrations;

    public function testGetOrders()
    {
        factory(\WA\DataStore\Order\Order::class, 40)->create();

        $res = $this->json('GET', 'orders');

        $res->seeJsonStructure([
                'data' => [
                    0 => ['type', 'id',
                        'attributes' => [
                            'status',
                            'orderType',
                            'serviceImei',
                            'servicePhoneNo',
                            'serviceSim',
                            'deviceImei',
                            'deviceCarrier',
                            'deviceSim',
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
        $order = factory(\WA\DataStore\Order\Order::class)->create(['userId' => $this->mainUser->id]);

        $res = $this->json('GET', 'orders/'.$order->id)
            ->seeJson([
                'type' => 'orders',
                'orderType' => $order->orderType,
                'serviceImei' => "$order->serviceImei",
                'servicePhoneNo' => "$order->servicePhoneNo",
                'serviceSim' => "$order->serviceSim",
                'deviceImei' => "$order->deviceImei",
                'deviceCarrier' => $order->deviceCarrier,
                'deviceSim' => "$order->deviceSim",
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
                        'orderType',
                        'serviceImei',
                        'servicePhoneNo',
                        'serviceSim',
                        'deviceImei',
                        'deviceCarrier',
                        'deviceSim',
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
        $package = factory(\WA\DataStore\Package\Package::class)->create();
        $service = factory(\WA\DataStore\Service\Service::class)->create();
        $address = factory(\WA\DataStore\Address\Address::class)->create();
        $order = factory(\WA\DataStore\Order\Order::class)->create(['userId' => $this->mainUser->id, 'packageId' => $package->id, 'serviceId' => $service->id, 'addressId' => $address->id]);

        $app1 = factory(\WA\DataStore\App\App::class)->create()->id;
        $app2 = factory(\WA\DataStore\App\App::class)->create()->id;
        $dataApps = array($app1, $app2);
        $order->apps()->sync($dataApps);

        $deviceVariation1 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create()->id;
        $deviceVariation2 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create()->id;
        $dataDeviceVariations = array($deviceVariation1, $deviceVariation2);
        $order->deviceVariations()->sync($dataDeviceVariations);

        $res = $this->json('GET', '/orders/'.$order->id.'?include=packages,services,users,devicevariations,devicevariations.carriers,devicevariations.devices,devicevariations.devices.devicetypes,addresses,apps');
        //\Log::debug("testGetOrderByIdIncludeAll: ".print_r($res->response->getContent(), true));
        $res->seeJsonStructure([
                'data' => [
                    'type',
                    'id',
                    'attributes' => [
                        'status',
                        'orderType',
                        'serviceImei',
                        'servicePhoneNo',
                        'serviceSim',
                        'deviceImei',
                        'deviceCarrier',
                        'deviceSim',
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
                        ],
                        'addresses' => [
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
                            'information',
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
                    ],
                    7 => [ // ADDRESS
                        'type',
                        'id',
                        'attributes' => [
                            'name',
                            'attn',
                            'phone',
                            'address',
                            'city',
                            'state',
                            'country',
                            'postalCode',
                        ],
                        'links' => [
                            'self',
                        ],
                    ]
                ]
            ]);
    }

    public function testCreateOrderWithPackageAndService()
    {
        $userId = factory(\WA\DataStore\User\User::class)->create(['companyId' => 1])->id;

        $userAdmin = factory(\WA\DataStore\User\User::class)->create(['companyId' => 1]);
        $userAdmin->roles()->sync([$this->roleAdmin->id]);

        $packageId = factory(\WA\DataStore\Package\Package::class)->create()->id;
        $serviceId = factory(\WA\DataStore\Service\Service::class)->create()->id;
        $addressId = factory(\WA\DataStore\Address\Address::class)->create()->id;

        $app1 = factory(\WA\DataStore\App\App::class)->create()->id;
        $app2 = factory(\WA\DataStore\App\App::class)->create()->id;

        $deviceVariation1 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create()->id;
        $deviceVariation2 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create()->id;

        $res = $this->json('POST', '/orders?include=packages,services,users,devicevariations,devicevariations.carriers,devicevariations.devices,devicevariations.devices.devicetypes,addresses,apps',
            [
                'data' => [
                    'type' => 'orders',
                    'attributes' => [
                        'status' => 'Enabled',
                        'orderType' => 'UpgradeDevice',
                        'serviceImei' => '222222222',
                        'servicePhoneNo' => '111111111',
                        'serviceSim' => '3333333333',
                        'deviceImei' => '222222222',
                        'deviceCarrier' => 'New Carrier',
                        'deviceSim' => '3333333333',
                        'userId' => $userId,
                        'packageId' => $packageId,
                        'serviceId' => $serviceId,
                        'addressId' => $addressId
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
            );
            //Log::debug("testCreateUser: ".print_r($res->response->getContent(), true));
            $res->seeJson(
                [
                    'status' => 'Approval',
                    'orderType' => 'UpgradeDevice',
                    'serviceImei' => '222222222',
                    'servicePhoneNo' => '111111111',
                    'serviceSim' => '3333333333',
                    'deviceImei' => '222222222',
                    'deviceCarrier' => 'New Carrier',
                    'deviceSim' => '3333333333',
                    'userId' => $userId,
                    'packageId' => $packageId,
                    'serviceId' => $serviceId,
                    'addressId' => $addressId
                ])
            ->seeJsonStructure(
                [
                    'data' => [
                        'type',
                        'id',
                        'attributes' => [
                            'status',
                            'orderType',
                            'serviceImei',
                            'servicePhoneNo',
                            'serviceSim',
                            'deviceImei',
                            'deviceCarrier',
                            'deviceSim',
                            'userId',
                            'packageId',
                            'serviceId',
                            'addressId'
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
                            ],
                            'addresses' => [
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
                                'information',
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
                        ],
                        7 => [ // ADDRESS
                            'type',
                            'id',
                            'attributes' => [
                                'name',
                                'attn',
                                'phone',
                                'address',
                                'city',
                                'state',
                                'country',
                                'postalCode',
                            ],
                            'links' => [
                                'self',
                            ],
                        ]
                    ]
                ]);
    }

    public function testCreateOrderWithoutPackage()
    {
        $userId = factory(\WA\DataStore\User\User::class)->create(['companyId' => 1])->id;

        $userAdmin = factory(\WA\DataStore\User\User::class)->create(['companyId' => 1]);
        $userAdmin->roles()->sync([$this->roleAdmin->id]);

        $packageId = factory(\WA\DataStore\Package\Package::class)->create()->id;
        $serviceId = factory(\WA\DataStore\Service\Service::class)->create()->id;
        $addressId = factory(\WA\DataStore\Address\Address::class)->create()->id;

        $app1 = factory(\WA\DataStore\App\App::class)->create()->id;
        $app2 = factory(\WA\DataStore\App\App::class)->create()->id;

        $deviceVariation1 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create()->id;
        $deviceVariation2 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create()->id;

        $res = $this->json('POST', '/orders?include=packages,services,users,devicevariations,devicevariations.carriers,devicevariations.devices,devicevariations.devices.devicetypes,addresses,apps',
            [
                'data' => [
                    'type' => 'orders',
                    'attributes' => [
                        'status' => 'Enabled',
                        'orderType' => 'UpgradeDevice',
                        'serviceImei' => '222222222',
                        'servicePhoneNo' => '111111111',
                        'serviceSim' => '3333333333',
                        'deviceImei' => '222222222',
                        'deviceCarrier' => 'New Carrier',
                        'deviceSim' => '3333333333',
                        'userId' => $userId,
                        'packageId' => null,
                        'serviceId' => $serviceId,
                        'addressId' => $addressId
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
            );
            //Log::debug("testCreateOrderWithoutPackage: ".print_r($res->response->getContent(), true));
            $res->seeJson(
                [
                    'status' => 'Approval',
                    'orderType' => 'UpgradeDevice',
                    'serviceImei' => '222222222',
                    'servicePhoneNo' => '111111111',
                    'serviceSim' => '3333333333',
                    'deviceImei' => '222222222',
                    'deviceCarrier' => 'New Carrier',
                    'deviceSim' => '3333333333',
                    'userId' => $userId,
                    'packageId' => null,
                    'serviceId' => $serviceId,
                    'addressId' => $addressId
                ])
            ->seeJsonStructure(
                [
                    'data' => [
                        'type',
                        'id',
                        'attributes' => [
                            'status',
                            'orderType',
                            'serviceImei',
                            'servicePhoneNo',
                            'serviceSim',
                            'deviceImei',
                            'deviceCarrier',
                            'deviceSim',
                            'userId',
                            'packageId',
                            'serviceId',
                            'addressId'
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
                                'data' => [],
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
                            ],
                            'addresses' => [
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
                        1 => [ // SERVICE
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
                        2 => [ // APP
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
                        6 => [ // ADDRESS
                            'type',
                            'id',
                            'attributes' => [
                                'name',
                                'attn',
                                'phone',
                                'address',
                                'city',
                                'state',
                                'country',
                                'postalCode',
                            ],
                            'links' => [
                                'self',
                            ],
                        ]
                    ]
                ]);
    }

    public function testCreateOrderWithoutService()
    {
        $userId = factory(\WA\DataStore\User\User::class)->create(['companyId' => 1])->id;

        $userAdmin = factory(\WA\DataStore\User\User::class)->create(['companyId' => 1]);
        $userAdmin->roles()->sync([$this->roleAdmin->id]);

        $packageId = factory(\WA\DataStore\Package\Package::class)->create()->id;
        $serviceId = factory(\WA\DataStore\Service\Service::class)->create()->id;
        $addressId = factory(\WA\DataStore\Address\Address::class)->create()->id;

        $app1 = factory(\WA\DataStore\App\App::class)->create()->id;
        $app2 = factory(\WA\DataStore\App\App::class)->create()->id;

        $deviceVariation1 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create()->id;
        $deviceVariation2 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create()->id;

        $res = $this->json('POST', '/orders?include=packages,services,users,devicevariations,devicevariations.carriers,devicevariations.devices,devicevariations.devices.devicetypes,addresses,apps',
            [
                'data' => [
                    'type' => 'orders',
                    'attributes' => [
                        'status' => 'Enabled',
                        'orderType' => 'UpgradeDevice',
                        'serviceImei' => '222222222',
                        'servicePhoneNo' => '111111111',
                        'serviceSim' => '3333333333',
                        'deviceImei' => '222222222',
                        'deviceCarrier' => 'New Carrier',
                        'deviceSim' => '3333333333',
                        'userId' => $userId,
                        'packageId' => $packageId,
                        'serviceId' => null,
                        'addressId' => $addressId
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
            );
            //Log::debug("testCreateUser: ".print_r($res->response->getContent(), true));
            $res->seeJson(
                [
                    'status' => 'Approval',
                    'orderType' => 'UpgradeDevice',
                    'serviceImei' => '222222222',
                    'servicePhoneNo' => '111111111',
                    'serviceSim' => '3333333333',
                    'deviceImei' => '222222222',
                    'deviceCarrier' => 'New Carrier',
                    'deviceSim' => '3333333333',
                    'userId' => $userId,
                    'packageId' => $packageId,
                    'serviceId' => null,
                    'addressId' => $addressId
                ])
            ->seeJsonStructure(
                [
                    'data' => [
                        'type',
                        'id',
                        'attributes' => [
                            'status',
                            'orderType',
                            'serviceImei',
                            'servicePhoneNo',
                            'serviceSim',
                            'deviceImei',
                            'deviceCarrier',
                            'deviceSim',
                            'userId',
                            'packageId',
                            'serviceId',
                            'addressId'
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
                                'data' => [],
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
                            'addresses' => [
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
                                'information',
                                'companyId'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        2 => [ // APP
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
                        6 => [ // ADDRESS
                            'type',
                            'id',
                            'attributes' => [
                                'name',
                                'attn',
                                'phone',
                                'address',
                                'city',
                                'state',
                                'country',
                                'postalCode',
                            ],
                            'links' => [
                                'self',
                            ],
                        ]
                    ]
                ]);
    }

    public function testUpdateOrder()
    {
        $order1 = factory(\WA\DataStore\Order\Order::class)->create([
            'status' => 'status1',
            'orderType' => 'NewLineOfService',
            'serviceImei' => '1010101010',
            'servicePhoneNo' => '1010101010',
            'serviceSim' => '1010101010',
            'deviceImei' => '1010101010',
            'deviceCarrier' => 'New Carrier Order 1',
            'deviceSim' => '1010101010',
            'userId' => 1,
            'packageId' => 1,
            'serviceId' => 1,
            'addressId' => 1
            ]);
        $order2 = factory(\WA\DataStore\Order\Order::class)->create([
            'status' => 'status2',
            'orderType' => 'UpgradeDevice',
            'serviceImei' => '2020202020',
            'servicePhoneNo' => '2020202020',
            'serviceSim' => '2020202020',
            'deviceImei' => '2020202020',
            'deviceCarrier' => 'New Carrier Order 2',
            'deviceSim' => '2020202020',
            'userId' => 2,
            'packageId' => 2,
            'serviceId' => 2,
            'addressId' => 2
            ]);

        $this->assertNotEquals($order1->id, $order2->id);
        $this->assertNotEquals($order1->status, $order2->status);
        $this->assertNotEquals($order1->orderType, $order2->orderType);
        $this->assertNotEquals($order1->serviceImei, $order2->serviceImei);
        $this->assertNotEquals($order1->servicePhoneNo, $order2->servicePhoneNo);
        $this->assertNotEquals($order1->serviceSim, $order2->serviceSim);
        $this->assertNotEquals($order1->deviceImei, $order2->deviceImei);
        $this->assertNotEquals($order1->deviceCarrier, $order2->deviceCarrier);
        $this->assertNotEquals($order1->deviceSim, $order2->deviceSim);
        $this->assertNotEquals($order1->userId, $order2->userId);
        $this->assertNotEquals($order1->packageId, $order2->packageId);
        $this->assertNotEquals($order1->serviceId, $order2->serviceId);
        $this->assertNotEquals($order1->addressId, $order2->addressId);

        $this->PATCH('/orders/'.$order1->id,
            [
                'data' => [
                    'type' => 'orders',
                    'attributes' => [
                        'status' => $order2->status,
                        'orderType' => $order2->orderType,
                        'serviceImei' => $order2->serviceImei,
                        'servicePhoneNo' => $order2->servicePhoneNo,
                        'serviceSim' => $order2->serviceSim,
                        'deviceImei' => $order2->deviceImei,
                        'deviceCarrier' => $order2->deviceCarrier,
                        'deviceSim' => $order2->deviceSim,
                        'userId' => $order2->userId,
                        'packageId' => $order2->packageId,
                        'serviceId' => $order2->serviceId,
                    ],
                ],
            ])
            ->seeJson([
                'type' => 'orders',
                'id' => "$order1->id",
                'status' => $order1->status, // The Status will not be updated directly.
                'orderType' => $order2->orderType,
                'serviceImei' => $order2->serviceImei,
                'servicePhoneNo' => $order2->servicePhoneNo,
                'serviceSim' => $order2->serviceSim,
                'deviceImei' => $order2->deviceImei,
                'deviceCarrier' => $order2->deviceCarrier,
                'deviceSim' => $order2->deviceSim,
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
        $addressId = factory(\WA\DataStore\Address\Address::class)->create()->id;

        $order = factory(\WA\DataStore\Order\Order::class)->create();

        $app1 = factory(\WA\DataStore\App\App::class)->create()->id;
        $app2 = factory(\WA\DataStore\App\App::class)->create()->id;
        $order->apps()->sync(array($app1, $app2));

        $orderAppDB = DB::table('order_apps')->where('orderId', $order->id)->get();
        $this->assertCount(2, $orderAppDB);
        $this->assertEquals($orderAppDB[0]->appId, $app1);
        $this->assertEquals($orderAppDB[1]->appId, $app2);

        DB::table('device_types')->insert(['name' => 'Smartphone', 'statusId' => 1]);
        $deviceTypes = DB::table('device_types')->get();
        $deviceId = factory(\WA\DataStore\Device\Device::class)->create(['deviceTypeId' => $deviceTypes[0]->id])->id;
        $deviceVariation1 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['deviceId' => $deviceId])->id;
        $deviceVariation2 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['deviceId' => $deviceId])->id;
        $order->deviceVariations()->sync(array($deviceVariation1, $deviceVariation2));

        $orderDDB = DB::table('order_device_variations')->where('orderId', $order->id)->get();
        $this->assertCount(2, $orderDDB);
        $this->assertEquals($orderDDB[0]->deviceVariationId, $deviceVariation1);
        $this->assertEquals($orderDDB[1]->deviceVariationId, $deviceVariation2);

        $res = $this->json('PATCH', '/orders/'.$order->id.'?include=packages,services,users,devicevariations,devicevariations.carriers,devicevariations.devices,devicevariations.devices.devicetypes,addresses,apps',
            [
                'data' => [
                    'type' => 'orders',
                    'attributes' => [
                        'status' => 'Enabled',
                        'orderType' => $order->orderType,
                        'serviceImei' => $order->serviceImei,
                        'servicePhoneNo' => $order->servicePhoneNo,
                        'serviceSim' => $order->serviceSim,
                        'deviceImei' => $order->deviceImei,
                        'deviceCarrier' => $order->deviceCarrier,
                        'deviceSim' => $order->deviceSim,
                        'userId' => $userId,
                        'packageId' => $packageId,
                        'serviceId' => $serviceId,
                        'addressId' => $addressId
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
            );
            //Log::debug("RES TEST: ".print_r($res->response->getContent(), true));
            $res->seeJson(
                [
                    'status' => 'Enabled',
                    'orderType' => $order->orderType,
                    'serviceImei' => $order->serviceImei,
                    'servicePhoneNo' => $order->servicePhoneNo,
                    'serviceSim' => $order->serviceSim,
                    'deviceImei' => $order->deviceImei,
                    'deviceCarrier' => $order->deviceCarrier,
                    'deviceSim' => $order->deviceSim,
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
                            'orderType',
                            'serviceImei',
                            'servicePhoneNo',
                            'serviceSim',
                            'deviceImei',
                            'deviceCarrier',
                            'deviceSim',
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
                            ],
                            'addresses' => [
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
                        0 => [ // DEVICETYPE
                            'type',
                            'id',
                            'attributes' => [
                                'name',
                                'statusId'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        1 => [ // DEVICE
                            'type',
                            'id',
                            'attributes' => [
                                'name',
                                'properties',
                                'statusId',
                                'externalId',
                                'identification',
                                'syncId',
                                'make',
                                'model',
                                'defaultPrice',
                                'currency'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        2 => [ // USER
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
                        3 => [ // PACKAGE
                            'type',
                            'id',
                            'attributes' => [
                                'name',
                                'information',
                                'companyId'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        4 => [ // SERVICE
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
                        7 => [ // ADDRESS
                            'type',
                            'id',
                            'attributes' => [
                                'name',
                                'attn',
                                'phone',
                                'address',
                                'city',
                                'state',
                                'country',
                                'postalCode',
                            ],
                            'links' => [
                                'self',
                            ],
                        ]
                    ]
                ]);
    }

    public function testUpdateUserAddRelationships()
    {
        $userId = factory(\WA\DataStore\User\User::class)->create()->id;
        $packageId = factory(\WA\DataStore\Package\Package::class)->create()->id;
        $serviceId = factory(\WA\DataStore\Service\Service::class)->create()->id;
        $addressId = factory(\WA\DataStore\Address\Address::class)->create()->id;

        $order = factory(\WA\DataStore\Order\Order::class)->create();

        $app1 = factory(\WA\DataStore\App\App::class)->create()->id;
        $app2 = factory(\WA\DataStore\App\App::class)->create()->id;
        $order->apps()->sync(array($app1, $app2));

        $orderAppDB = DB::table('order_apps')->where('orderId', $order->id)->get();
        $this->assertCount(2, $orderAppDB);
        $this->assertEquals($orderAppDB[0]->appId, $app1);
        $this->assertEquals($orderAppDB[1]->appId, $app2);

        $app3 = factory(\WA\DataStore\App\App::class)->create()->id;

        DB::table('device_types')->insert(['name' => 'Smartphone', 'statusId' => 1]);
        $deviceTypes = DB::table('device_types')->get();
        $deviceId = factory(\WA\DataStore\Device\Device::class)->create(['deviceTypeId' => $deviceTypes[0]->id])->id;
        $deviceVariation1 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['deviceId' => $deviceId])->id;
        $deviceVariation2 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create(['deviceId' => $deviceId])->id;
        $order->deviceVariations()->sync(array($deviceVariation1, $deviceVariation2));

        $orderDDB = DB::table('order_device_variations')->where('orderId', $order->id)->get();
        $this->assertCount(2, $orderDDB);
        $this->assertEquals($orderDDB[0]->deviceVariationId, $deviceVariation1);
        $this->assertEquals($orderDDB[1]->deviceVariationId, $deviceVariation2);

        $deviceVariation3 = factory(\WA\DataStore\DeviceVariation\DeviceVariation::class)->create()->id;

        $res = $this->json('PATCH', '/orders/'.$order->id.'?include=packages,services,users,devicevariations,devicevariations.carriers,devicevariations.devices,devicevariations.devices.devicetypes,addresses,apps',
            [
                'data' => [
                    'type' => 'orders',
                    'attributes' => [
                        'status' => 'Enabled',
                        'orderType' => $order->orderType,
                        'serviceImei' => $order->serviceImei,
                        'servicePhoneNo' => $order->servicePhoneNo,
                        'serviceSim' => $order->serviceSim,
                        'deviceImei' => $order->deviceImei,
                        'deviceCarrier' => $order->deviceCarrier,
                        'deviceSim' => $order->deviceSim,
                        'userId' => $userId,
                        'packageId' => $packageId,
                        'serviceId' => $serviceId,
                        'addressId' => $addressId
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
            );
            //Log::debug("RES TEST: ".print_r($res->response->getContent(), true));
            $res->seeJson(
                [
                    'status' => 'Enabled',
                    'orderType' => $order->orderType,
                    'serviceImei' => $order->serviceImei,
                    'servicePhoneNo' => $order->servicePhoneNo,
                    'serviceSim' => $order->serviceSim,
                    'deviceImei' => $order->deviceImei,
                    'deviceCarrier' => $order->deviceCarrier,
                    'deviceSim' => $order->deviceSim,
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
                            'orderType',
                            'serviceImei',
                            'servicePhoneNo',
                            'serviceSim',
                            'deviceImei',
                            'deviceCarrier',
                            'deviceSim',
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
                            ],
                            'addresses' => [
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
                        0 => [ // DEVICETYPE
                            'type',
                            'id',
                            'attributes' => [
                                'name',
                                'statusId'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        1 => [ // DEVICE
                            'type',
                            'id',
                            'attributes' => [
                                'name',
                                'properties',
                                'statusId',
                                'externalId',
                                'identification',
                                'syncId',
                                'make',
                                'model',
                                'defaultPrice',
                                'currency'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        2 => [ // USER
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
                        3 => [ // PACKAGE
                            'type',
                            'id',
                            'attributes' => [
                                'name',
                                'information',
                                'companyId'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        4 => [ // SERVICE
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
                        6 => [ // APP
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
                        7 => [ // APP
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
                        ],
                        9 => [ // DEVICEVARIATIONS
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
                        10 => [ // DEVICEVARIATIONS
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
                        11 => [ // ADDRESS
                            'type',
                            'id',
                            'attributes' => [
                                'name',
                                'attn',
                                'phone',
                                'address',
                                'city',
                                'state',
                                'country',
                                'postalCode',
                            ],
                            'links' => [
                                'self',
                            ],
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
