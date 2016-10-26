<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use WA\DataStore\Device\Device;
use WA\DataStore\Company\Company;
use WA\DataStore\Carrier\Carrier;

class DevicesApiTest extends TestCase
{
    use DatabaseMigrations;

    public function testGetDevices()
    {
        factory(\WA\DataStore\Device\Device::class, 40)->create();

        $res = $this->json('GET', 'devices');

        $res->seeJsonStructure([
            'data' => [
                0 => [
                    'type',
                    'id',
                    'attributes' => [
                        'identification',
                        'name',
                        'properties',
                        'externalId',
                        'statusId',
                        'syncId',
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

    public function testGetDeviceByIdIfExists()
    {
        $device = factory(\WA\DataStore\Device\Device::class)->create();

        $res = $this->json('GET', 'devices/'.$device->id)
            ->seeJson([
                'type' => 'devices',
                'identification' => $device->identification,
                'name' => $device->name,
                'properties' => $device->properties,
                'externalId' => $device->externalId,
                'statusId' => $device->statusId,
                'syncId' => $device->syncId,
            ]);

        $res->seeJsonStructure([
            'data' => [
                'type',
                'id',
                'attributes' => [
                    'identification',
                    'name',
                    'properties',
                    'externalId',
                    'statusId',
                    'syncId',
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
        ]);
    }

    public function testGetDeviceByIdIfNoExists()
    {
        $deviceId = factory(\WA\DataStore\Device\Device::class)->create()->id;
        $deviceId = $deviceId + 10;

        $response = $this->call('GET', '/devices/'.$deviceId);
        $this->assertEquals(404, $response->status());
    }

    public function testGetDeviceByIdandIncludesAssets()
    {
        $device = factory(\WA\DataStore\Device\Device::class)->create();

        $asset1 = factory(\WA\DataStore\Asset\Asset::class)->create()->id;
        $asset2 = factory(\WA\DataStore\Asset\Asset::class)->create()->id;

        $dataAssets = array($asset1, $asset2);

        $device->assets()->sync($dataAssets);

        $response = $this->json('GET', 'devices/'.$device->id.'?include=assets')
            ->seeJsonStructure([
                'data' => [
                    'type',
                    'id',
                    'attributes' => [
                        'identification',
                        'name',
                        'properties',
                        'externalId',
                        'statusId',
                        'syncId',
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
                        'assets' => [
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
                            'identification',
                            'active',
                            'statusId',
                            'typeId',
                            'externalId',
                            'carrierId',
                            'syncId',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],

                ],
            ]);
    }

    public function testGetDeviceByIdandIncludesImages()
    {
        $device = factory(\WA\DataStore\Device\Device::class)->create();

        $image1 = factory(\WA\DataStore\Image\Image::class)->create()->id;
        $image2 = factory(\WA\DataStore\Image\Image::class)->create()->id;

        $dataImages = array($image1, $image2);

        $device->images()->sync($dataImages);

        $response = $this->get('/devices/'.$device->id.'?include=images')
            ->seeJsonStructure([
                'data' => [
                    'type',
                    'id',
                    'attributes' => [
                        'identification',
                        'name',
                        'properties',
                        'externalId',
                        'statusId',
                        'syncId',
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
                        'images' => [
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
                            'originalName',
                            'filename',
                            'mimeType',
                            'extension',
                            'size',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],

                ],
            ]);
    }

    public function testGetDeviceByIdandIncludesCompanies()
    {
        $device = factory(\WA\DataStore\Device\Device::class)->create();

        $company1 = factory(\WA\DataStore\Company\Company::class)->create()->id;
        $company2 = factory(\WA\DataStore\Company\Company::class)->create()->id;

        $dataCompanies = array($company1, $company2);

        $device->companies()->sync($dataCompanies);

        $response = $this->get('/devices/'.$device->id.'?include=companies')
            ->seeJsonStructure([
                'data' => [
                    'type',
                    'id',
                    'attributes' => [
                        'identification',
                        'name',
                        'properties',
                        'externalId',
                        'statusId',
                        'syncId',
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
                        'companies' => [
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
                            'label',
                            'active',
                            'udlpath',
                            'isCensus',
                            'udlPathRule',
                            'assetPath',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],

                ],
            ]);
    }

    public function testGetDeviceByIdandIncludesCarriers()
    {
        $device = factory(\WA\DataStore\Device\Device::class)->create();

        $carrier1 = factory(\WA\DataStore\Carrier\Carrier::class)->create()->id;
        $carrier2 = factory(\WA\DataStore\Carrier\Carrier::class)->create()->id;

        $dataCarriers = array($carrier1, $carrier2);

        $device->carriers()->sync($dataCarriers);

        $response = $this->get('/devices/'.$device->id.'?include=carriers')
            ->seeJsonStructure([
                'data' => [
                    'type',
                    'id',
                    'attributes' => [
                        'identification',
                        'name',
                        'properties',
                        'externalId',
                        'statusId',
                        'syncId',
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
                    ],
                ],
                'included' => [
                    1 => [
                        'type',
                        'id',
                        'attributes' => [
                            'name',
                            'presentation',
                            'active',
                            'locationId',
                            'shortName',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],

                ],
            ]);
    }

    public function testCreateDevice()
    {
        $device = factory(\WA\DataStore\Device\Device::class)->create();

        $imag1 = factory(\WA\DataStore\Image\Image::class)->create()->id;
        $imag2 = factory(\WA\DataStore\Image\Image::class)->create()->id;

        $asset1 = factory(\WA\DataStore\Asset\Asset::class)->create()->id;
        $asset2 = factory(\WA\DataStore\Asset\Asset::class)->create()->id;

        $modCap1 = factory(\WA\DataStore\Modification\Modification::class)->create(['modType' => 'capacity'])->id;
        $modSty2 = factory(\WA\DataStore\Modification\Modification::class)->create(['modType' => 'style'])->id;
        $modCap3 = factory(\WA\DataStore\Modification\Modification::class)->create(['modType' => 'capacity'])->id;

        $carr1 = factory(\WA\DataStore\Carrier\Carrier::class)->create()->id;
        $carr2 = factory(\WA\DataStore\Carrier\Carrier::class)->create()->id;

        $comp1 = factory(\WA\DataStore\Company\Company::class)->create()->id;
        $comp2 = factory(\WA\DataStore\Company\Company::class)->create()->id;

        $device = $this->post('/devices',
            [
                'data' => [
                    'type' => 'devices',
                    'attributes' => [
                        'name' => 'whenIneedMotivation...',
                        'properties' => 'MyOneSolutionIsMyQueen',
                        'deviceTypeId' => $device->deviceTypeId,
                        'statusId' => 1,
                        'externalId' => 2,
                        'identification' => rand(9000000000000, 9999999999999),
                    ],
                    'relationships' => [
                        'images' => [
                            'data' => [
                                ['type' => 'images', 'id' => $imag1],
                                ['type' => 'images', 'id' => $imag2],
                            ],
                        ],
                        'assets' => [
                            'data' => [
                                ['type' => 'assets', 'id' => $asset1],
                                ['type' => 'assets', 'id' => $asset2],
                            ],
                        ],
                        'modifications' => [
                            'data' => [
                                ['type' => 'modifications', 'id' => $modCap1],
                                ['type' => 'modifications', 'id' => $modSty2],
                                ['type' => 'modifications', 'id' => $modCap3],
                            ],
                        ],
                        'carriers' => [
                            'data' => [
                                ['type' => 'carriers', 'id' => $carr1],
                                ['type' => 'carriers', 'id' => $carr2],
                            ],
                        ],
                        'companies' => [
                            'data' => [
                                ['type' => 'companies', 'id' => $comp1],
                                ['type' => 'companies', 'id' => $comp2],
                            ],
                        ],
                        'prices' => [
                            'data' => [
                                ['type' => 'prices', 'capacityId' => $modCap1, 'styleId' => $modSty2, 'carrierId' => $carr1, 'companyId' => $comp1, 'priceRetail' => 100, 'price1' => 100, 'price2' => 100, 'priceOwn' => 100],
                                ['type' => 'prices', 'capacityId' => $modCap1, 'styleId' => $modSty2, 'carrierId' => $carr1, 'companyId' => $comp2, 'priceRetail' => 200, 'price1' => 200, 'price2' => 200, 'priceOwn' => 200],
                                ['type' => 'prices', 'capacityId' => $modCap1, 'styleId' => $modSty2, 'carrierId' => $carr2, 'companyId' => $comp1, 'priceRetail' => 300, 'price1' => 300, 'price2' => 300, 'priceOwn' => 300],
                                ['type' => 'prices', 'capacityId' => $modCap1, 'styleId' => $modSty2, 'carrierId' => $carr2, 'companyId' => $comp2, 'priceRetail' => 400, 'price1' => 400, 'price2' => 400, 'priceOwn' => 400],
                                ['type' => 'prices', 'capacityId' => $modCap3, 'styleId' => $modSty2, 'carrierId' => $carr1, 'companyId' => $comp1, 'priceRetail' => 500, 'price1' => 500, 'price2' => 500, 'priceOwn' => 500],
                                ['type' => 'prices', 'capacityId' => $modCap3, 'styleId' => $modSty2, 'carrierId' => $carr1, 'companyId' => $comp2, 'priceRetail' => 600, 'price1' => 600, 'price2' => 600, 'priceOwn' => 600],
                                ['type' => 'prices', 'capacityId' => $modCap3, 'styleId' => $modSty2, 'carrierId' => $carr2, 'companyId' => $comp1, 'priceRetail' => 700, 'price1' => 700, 'price2' => 700, 'priceOwn' => 700],
                                ['type' => 'prices', 'capacityId' => $modCap3, 'styleId' => $modSty2, 'carrierId' => $carr2, 'companyId' => $comp2, 'priceRetail' => 800, 'price1' => 800, 'price2' => 800, 'priceOwn' => 800],
                            ],
                        ],
                    ],
                ],
            ]
            )->seeJson(
            [
                'type' => 'devices',
                'name' => 'whenIneedMotivation...',
                'properties' => 'MyOneSolutionIsMyQueen',
                'statusId' => 1,
                'externalId' => 2,
            ]);
    }

    public function testCreateDeviceReturnNoValidData()
    {
        // 'data' no valid.
        $device = $this->post('/devices',
            [
                'NoValid' => [
                    ],
            ]
            )->seeJson(
            [
                'errors' => [
                    'json' => 'JSON is Invalid',
                ],
            ]
        );
    }

    public function testCreateDeviceReturnNoValidType()
    {
        // 'type' no valid.
        $device = $this->post('/devices',
            [
                'data' => [
                    'NoValid' => 'devices',
                    'attributes' => [
                        'name' => 'whenIneedMotivation...',
                        'properties' => 'MyOneSolutionIsMyQueen',
                    ],
                ],

            ]
            )->seeJson(
            [
                'errors' => [
                    'json' => 'JSON is Invalid',
                ],
            ]
        );
    }

    public function testCreateDeviceReturnNoValidAttributes()
    {
        // 'attributes' no valid.
        $device = $this->post('/devices',
            [
                'data' => [
                    'type' => 'devices',
                    'NoValid' => [
                        'name' => 'whenIneedMotivation...',
                        'properties' => 'MyOneSolutionIsMyQueen',
                    ],
                ],
            ]
            )->seeJson(
            [
                'errors' => [
                    'json' => 'JSON is Invalid',
                ],
            ]
        );
    }

    public function testCreateDeviceReturnRelationshipNoExists()
    {
        $device = factory(\WA\DataStore\Device\Device::class)->create();
        $deviceTypeId = $device->deviceTypeId;

        $device = $this->json('POST', '/devices',
        [
            'data' => [
                'type' => 'devices',
                'attributes' => [
                    'identification' => rand(9000000000000, 9999999999999),
                    'name' => 'whenIneedMotivation...',
                    'properties' => 'MyOneSolutionIsMyQueen',
                    'deviceTypeId' => $deviceTypeId,
                ],
                'relationships' => [
                    'IgnoreType' => [
                        'data' => [
                            ['type' => 'assets', 'id' => '1'],
                            ['type' => 'assets', 'id' => '2'],
                        ],
                    ],
                ],
            ],
        ]
        )->seeJson(
        [
            'type' => 'devices',
            'name' => 'whenIneedMotivation...',
            'properties' => 'MyOneSolutionIsMyQueen',
        ]);
    }

    public function testCreateDeviceReturnRelationshipNoExistsData()
    {
        $device = factory(\WA\DataStore\Device\Device::class)->create();
        $deviceTypeId = $device->deviceTypeId;

        $device = $this->post('/devices',
        [
            'data' => [
                'type' => 'devices',
                'attributes' => [
                    'identification' => rand(9000000000000, 9999999999999),
                    'name' => 'whenIneedMotivation...',
                    'properties' => 'MyOneSolutionIsMyQueen',
                    'deviceTypeId' => $deviceTypeId,
                ],
                'relationships' => [
                    'assets' => [
                        'IgnoreType' => [
                            ['type' => 'assets', 'id' => '1'],
                            ['type' => 'assets', 'id' => '2'],
                        ],
                    ],
                ],
            ],
        ]
        )->seeJson(
        [
            'type' => 'devices',
            'name' => 'whenIneedMotivation...',
            'properties' => 'MyOneSolutionIsMyQueen',
        ]);
    }

    public function testCreateDeviceReturnRelationshipNoAssetsType()
    {
        $device = factory(\WA\DataStore\Device\Device::class)->create();
        $deviceTypeId = $device->deviceTypeId;

        $device = $this->post('/devices',
        [
            'data' => [
                'type' => 'devices',
                'attributes' => [
                    'identification' => rand(9000000000000, 9999999999999),
                    'name' => 'whenIneedMotivation...',
                    'properties' => 'MyOneSolutionIsMyQueen',
                    'deviceTypeId' => $deviceTypeId,
                ],
                'relationships' => [
                    'assets' => [
                        'data' => [
                            ['type' => 'NoAssets', 'id' => '1'],
                            ['type' => 'NoAssets', 'id' => '2'],
                        ],
                    ],
                ],
            ],
        ]
        )->seeJson(
        [
            'type' => 'devices',
            'name' => 'whenIneedMotivation...',
            'properties' => 'MyOneSolutionIsMyQueen',
        ]);
    }

    public function testCreateDeviceReturnRelationshipNoIdExists()
    {
        $device = factory(\WA\DataStore\Device\Device::class)->create();
        $deviceTypeId = $device->deviceTypeId;

        $device = $this->post('/devices',
        [
            'data' => [
                'type' => 'devices',
                'attributes' => [
                    'identification' => rand(9000000000000, 9999999999999),
                    'name' => 'whenIneedMotivation...',
                    'properties' => 'MyOneSolutionIsMyQueen',
                    'deviceTypeId' => $deviceTypeId,
                ],
                'relationships' => [
                    'assets' => [
                        'data' => [
                            ['type' => 'assets', 'aa' => '1'],
                            ['type' => 'assets', 'aa' => '2'],
                        ],
                    ],
                ],
            ],
        ]
        )->seeJson(
        [
            'type' => 'devices',
            'name' => 'whenIneedMotivation...',
            'properties' => 'MyOneSolutionIsMyQueen',
        ]);
    }

    public function testCreateDeviceReturnPriceModificationCapacityForeignKeyError()
    {
        $device = factory(\WA\DataStore\Device\Device::class)->create();
        $deviceTypeId = $device->deviceTypeId;

        $device = $this->post('/devices',
        [
            'data' => [
                'type' => 'devices',
                'attributes' => [
                    'identification' => rand(9000000000000, 9999999999999),
                    'name' => 'whenIneedMotivation...',
                    'properties' => 'MyOneSolutionIsMyQueen',
                    'deviceTypeId' => $deviceTypeId,
                ],
                'relationships' => [
                    'modifications' => [
                        'data' => [
                            ['type' => 'modifications', 'id' => 0],
                            ['type' => 'modifications', 'id' => 2],
                            ['type' => 'modifications', 'id' => 3],
                        ],
                    ],
                    'carriers' => [
                        'data' => [
                            ['type' => 'carriers', 'id' => 1],
                            ['type' => 'carriers', 'id' => 2],
                        ],
                    ],
                    'companies' => [
                        'data' => [
                            ['type' => 'companies', 'id' => 1],
                            ['type' => 'companies', 'id' => 2],
                        ],
                    ],
                    'prices' => [
                        'data' => [
                            ['type' => 'prices', 'capacityId' => 1, 'styleId' => 2, 'carrierId' => 1, 'companyId' => 1, 'priceRetail' => 100, 'price1' => 100, 'price2' => 100, 'priceOwn' => 100],
                            ['type' => 'prices', 'capacityId' => 1, 'styleId' => 2, 'carrierId' => 1, 'companyId' => 2, 'priceRetail' => 200, 'price1' => 200, 'price2' => 200, 'priceOwn' => 200],
                            ['type' => 'prices', 'capacityId' => 1, 'styleId' => 2, 'carrierId' => 2, 'companyId' => 1, 'priceRetail' => 300, 'price1' => 300, 'price2' => 300, 'priceOwn' => 300],
                            ['type' => 'prices', 'capacityId' => 1, 'styleId' => 2, 'carrierId' => 2, 'companyId' => 2, 'priceRetail' => 400, 'price1' => 400, 'price2' => 400, 'priceOwn' => 400],
                            ['type' => 'prices', 'capacityId' => 3, 'styleId' => 2, 'carrierId' => 1, 'companyId' => 1, 'priceRetail' => 500, 'price1' => 500, 'price2' => 500, 'priceOwn' => 500],
                            ['type' => 'prices', 'capacityId' => 3, 'styleId' => 2, 'carrierId' => 1, 'companyId' => 2, 'priceRetail' => 600, 'price1' => 600, 'price2' => 600, 'priceOwn' => 600],
                            ['type' => 'prices', 'capacityId' => 3, 'styleId' => 2, 'carrierId' => 2, 'companyId' => 1, 'priceRetail' => 700, 'price1' => 700, 'price2' => 700, 'priceOwn' => 700],
                            ['type' => 'prices', 'capacityId' => 3, 'styleId' => 2, 'carrierId' => 2, 'companyId' => 2, 'priceRetail' => 800, 'price1' => 800, 'price2' => 800, 'priceOwn' => 800],
                        ],
                    ],
                ],
            ],
        ]
        )->seeJson(
        [
            'errors' => [
                'prices' => 'the Device Prices has not been created',
            ],
        ]);
    }

    public function testCreateDeviceReturnPriceModificationStyleForeignKeyError()
    {
        $device = factory(\WA\DataStore\Device\Device::class)->create();
        $deviceTypeId = $device->deviceTypeId;

        $device = $this->post('/devices',
        [
            'data' => [
                'type' => 'devices',
                'attributes' => [
                    'identification' => rand(9000000000000, 9999999999999),
                    'name' => 'whenIneedMotivation...',
                    'properties' => 'MyOneSolutionIsMyQueen',
                    'deviceTypeId' => $deviceTypeId,
                ],
                'relationships' => [
                    'modifications' => [
                        'data' => [
                            ['type' => 'modifications', 'id' => 1],
                            ['type' => 'modifications', 'id' => 0],
                            ['type' => 'modifications', 'id' => 3],
                        ],
                    ],
                    'carriers' => [
                        'data' => [
                            ['type' => 'carriers', 'id' => 1],
                            ['type' => 'carriers', 'id' => 2],
                        ],
                    ],
                    'companies' => [
                        'data' => [
                            ['type' => 'companies', 'id' => 1],
                            ['type' => 'companies', 'id' => 2],
                        ],
                    ],
                    'prices' => [
                        'data' => [
                            ['type' => 'prices', 'capacityId' => 1, 'styleId' => 2, 'carrierId' => 1, 'companyId' => 1, 'priceRetail' => 100, 'price1' => 100, 'price2' => 100, 'priceOwn' => 100],
                            ['type' => 'prices', 'capacityId' => 1, 'styleId' => 2, 'carrierId' => 1, 'companyId' => 2, 'priceRetail' => 200, 'price1' => 200, 'price2' => 200, 'priceOwn' => 200],
                            ['type' => 'prices', 'capacityId' => 1, 'styleId' => 2, 'carrierId' => 2, 'companyId' => 1, 'priceRetail' => 300, 'price1' => 300, 'price2' => 300, 'priceOwn' => 300],
                            ['type' => 'prices', 'capacityId' => 1, 'styleId' => 2, 'carrierId' => 2, 'companyId' => 2, 'priceRetail' => 400, 'price1' => 400, 'price2' => 400, 'priceOwn' => 400],
                            ['type' => 'prices', 'capacityId' => 3, 'styleId' => 2, 'carrierId' => 1, 'companyId' => 1, 'priceRetail' => 500, 'price1' => 500, 'price2' => 500, 'priceOwn' => 500],
                            ['type' => 'prices', 'capacityId' => 3, 'styleId' => 2, 'carrierId' => 1, 'companyId' => 2, 'priceRetail' => 600, 'price1' => 600, 'price2' => 600, 'priceOwn' => 600],
                            ['type' => 'prices', 'capacityId' => 3, 'styleId' => 2, 'carrierId' => 2, 'companyId' => 1, 'priceRetail' => 700, 'price1' => 700, 'price2' => 700, 'priceOwn' => 700],
                            ['type' => 'prices', 'capacityId' => 3, 'styleId' => 2, 'carrierId' => 2, 'companyId' => 2, 'priceRetail' => 800, 'price1' => 800, 'price2' => 800, 'priceOwn' => 800],
                        ],
                    ],
                ],
            ],
        ]
        )->seeJson(
        [
            'errors' => [
                'prices' => 'the Device Prices has not been created',
            ],
        ]);
    }

    public function testCreateDeviceReturnPriceCarriersForeignKeyError()
    {
        $device = factory(\WA\DataStore\Device\Device::class)->create();
        $deviceTypeId = $device->deviceTypeId;

        $device = $this->post('/devices',
        [
            'data' => [
                'type' => 'devices',
                'attributes' => [
                    'identification' => rand(9000000000000, 9999999999999),
                    'name' => 'whenIneedMotivation...',
                    'properties' => 'MyOneSolutionIsMyQueen',
                    'deviceTypeId' => $deviceTypeId,
                ],
                'relationships' => [
                    'modifications' => [
                        'data' => [
                            ['type' => 'modifications', 'id' => 1],
                            ['type' => 'modifications', 'id' => 2],
                            ['type' => 'modifications', 'id' => 3],
                        ],
                    ],
                    'carriers' => [
                        'data' => [
                            ['type' => 'carriers', 'id' => 0],
                            ['type' => 'carriers', 'id' => 2],
                        ],
                    ],
                    'companies' => [
                        'data' => [
                            ['type' => 'companies', 'id' => 1],
                            ['type' => 'companies', 'id' => 2],
                        ],
                    ],
                    'prices' => [
                        'data' => [
                            ['type' => 'prices', 'capacityId' => 1, 'styleId' => 2, 'carrierId' => 1, 'companyId' => 1, 'priceRetail' => 100, 'price1' => 100, 'price2' => 100, 'priceOwn' => 100],
                            ['type' => 'prices', 'capacityId' => 1, 'styleId' => 2, 'carrierId' => 1, 'companyId' => 2, 'priceRetail' => 200, 'price1' => 200, 'price2' => 200, 'priceOwn' => 200],
                            ['type' => 'prices', 'capacityId' => 1, 'styleId' => 2, 'carrierId' => 2, 'companyId' => 1, 'priceRetail' => 300, 'price1' => 300, 'price2' => 300, 'priceOwn' => 300],
                            ['type' => 'prices', 'capacityId' => 1, 'styleId' => 2, 'carrierId' => 2, 'companyId' => 2, 'priceRetail' => 400, 'price1' => 400, 'price2' => 400, 'priceOwn' => 400],
                            ['type' => 'prices', 'capacityId' => 3, 'styleId' => 2, 'carrierId' => 1, 'companyId' => 1, 'priceRetail' => 500, 'price1' => 500, 'price2' => 500, 'priceOwn' => 500],
                            ['type' => 'prices', 'capacityId' => 3, 'styleId' => 2, 'carrierId' => 1, 'companyId' => 2, 'priceRetail' => 600, 'price1' => 600, 'price2' => 600, 'priceOwn' => 600],
                            ['type' => 'prices', 'capacityId' => 3, 'styleId' => 2, 'carrierId' => 2, 'companyId' => 1, 'priceRetail' => 700, 'price1' => 700, 'price2' => 700, 'priceOwn' => 700],
                            ['type' => 'prices', 'capacityId' => 3, 'styleId' => 2, 'carrierId' => 2, 'companyId' => 2, 'priceRetail' => 800, 'price1' => 800, 'price2' => 800, 'priceOwn' => 800],
                        ],
                    ],
                ],
            ],
        ]
        )->seeJson(
        [
            'errors' => [
                'prices' => 'the Device Prices has not been created',
            ],
        ]);
    }

    public function testCreateDeviceReturnPriceCompaniesForeignKeyError()
    {
        $device = factory(\WA\DataStore\Device\Device::class)->create();
        $deviceTypeId = $device->deviceTypeId;

        $device = $this->post('/devices',
        [
            'data' => [
                'type' => 'devices',
                'attributes' => [
                    'identification' => rand(9000000000000, 9999999999999),
                    'name' => 'whenIneedMotivation...',
                    'properties' => 'MyOneSolutionIsMyQueen',
                    'deviceTypeId' => $deviceTypeId,
                ],
                'relationships' => [
                    'modifications' => [
                        'data' => [
                            ['type' => 'modifications', 'id' => 1],
                            ['type' => 'modifications', 'id' => 2],
                            ['type' => 'modifications', 'id' => 3],
                        ],
                    ],
                    'carriers' => [
                        'data' => [
                            ['type' => 'carriers', 'id' => 1],
                            ['type' => 'carriers', 'id' => 2],
                        ],
                    ],
                    'companies' => [
                        'data' => [
                            ['type' => 'companies', 'id' => 0],
                            ['type' => 'companies', 'id' => 2],
                        ],
                    ],
                    'prices' => [
                        'data' => [
                            ['type' => 'prices', 'capacityId' => 1, 'styleId' => 2, 'carrierId' => 1, 'companyId' => 1, 'priceRetail' => 100, 'price1' => 100, 'price2' => 100, 'priceOwn' => 100],
                            ['type' => 'prices', 'capacityId' => 1, 'styleId' => 2, 'carrierId' => 1, 'companyId' => 2, 'priceRetail' => 200, 'price1' => 200, 'price2' => 200, 'priceOwn' => 200],
                            ['type' => 'prices', 'capacityId' => 1, 'styleId' => 2, 'carrierId' => 2, 'companyId' => 1, 'priceRetail' => 300, 'price1' => 300, 'price2' => 300, 'priceOwn' => 300],
                            ['type' => 'prices', 'capacityId' => 1, 'styleId' => 2, 'carrierId' => 2, 'companyId' => 2, 'priceRetail' => 400, 'price1' => 400, 'price2' => 400, 'priceOwn' => 400],
                            ['type' => 'prices', 'capacityId' => 3, 'styleId' => 2, 'carrierId' => 1, 'companyId' => 1, 'priceRetail' => 500, 'price1' => 500, 'price2' => 500, 'priceOwn' => 500],
                            ['type' => 'prices', 'capacityId' => 3, 'styleId' => 2, 'carrierId' => 1, 'companyId' => 2, 'priceRetail' => 600, 'price1' => 600, 'price2' => 600, 'priceOwn' => 600],
                            ['type' => 'prices', 'capacityId' => 3, 'styleId' => 2, 'carrierId' => 2, 'companyId' => 1, 'priceRetail' => 700, 'price1' => 700, 'price2' => 700, 'priceOwn' => 700],
                            ['type' => 'prices', 'capacityId' => 3, 'styleId' => 2, 'carrierId' => 2, 'companyId' => 2, 'priceRetail' => 800, 'price1' => 800, 'price2' => 800, 'priceOwn' => 800],
                        ],
                    ],
                ],
            ],
        ]
        )->seeJson(
        [
            'errors' => [
                'prices' => 'the Device Prices has not been created',
            ],
        ]);
    }

    public function testCreateDeviceReturnPriceCheckIfPriceRowIsNotCorrect()
    {
        $device = factory(\WA\DataStore\Device\Device::class)->create();
        $deviceTypeId = $device->deviceTypeId;

        $device = $this->post('/devices',
        [
            'data' => [
                'type' => 'devices',
                'attributes' => [
                    'identification' => rand(9000000000000, 9999999999999),
                    'name' => 'whenIneedMotivation...',
                    'properties' => 'MyOneSolutionIsMyQueen',
                    'deviceTypeId' => $deviceTypeId,
                ],
                'relationships' => [
                    'modifications' => [
                        'data' => [
                            ['type' => 'modifications', 'id' => 1],
                            ['type' => 'modifications', 'id' => 2],
                            ['type' => 'modifications', 'id' => 3],
                        ],
                    ],
                    'carriers' => [
                        'data' => [
                            ['type' => 'carriers', 'id' => 1],
                            ['type' => 'carriers', 'id' => 2],
                        ],
                    ],
                    'companies' => [
                        'data' => [
                            ['type' => 'companies', 'id' => 1],
                            ['type' => 'companies', 'id' => 2],
                        ],
                    ],
                    'prices' => [
                        'data' => [
                            ['type' => 'prices', 'capacityId' => 1000, 'styleId' => 2, 'carrierId' => 1, 'companyId' => 1, 'priceRetail' => 100, 'price1' => 100, 'price2' => 100, 'priceOwn' => 100],
                            ['type' => 'prices', 'capacityId' => 1, 'styleId' => 2, 'carrierId' => 1, 'companyId' => 2, 'priceRetail' => 200, 'price1' => 200, 'price2' => 200, 'priceOwn' => 200],
                            ['type' => 'prices', 'capacityId' => 1, 'styleId' => 2, 'carrierId' => 2, 'companyId' => 1, 'priceRetail' => 300, 'price1' => 300, 'price2' => 300, 'priceOwn' => 300],
                            ['type' => 'prices', 'capacityId' => 1, 'styleId' => 2, 'carrierId' => 2, 'companyId' => 2, 'priceRetail' => 400, 'price1' => 400, 'price2' => 400, 'priceOwn' => 400],
                            ['type' => 'prices', 'capacityId' => 3, 'styleId' => 2, 'carrierId' => 1, 'companyId' => 1, 'priceRetail' => 500, 'price1' => 500, 'price2' => 500, 'priceOwn' => 500],
                            ['type' => 'prices', 'capacityId' => 3, 'styleId' => 2, 'carrierId' => 1, 'companyId' => 2, 'priceRetail' => 600, 'price1' => 600, 'price2' => 600, 'priceOwn' => 600],
                            ['type' => 'prices', 'capacityId' => 3, 'styleId' => 2, 'carrierId' => 2, 'companyId' => 1, 'priceRetail' => 700, 'price1' => 700, 'price2' => 700, 'priceOwn' => 700],
                            ['type' => 'prices', 'capacityId' => 3, 'styleId' => 2, 'carrierId' => 2, 'companyId' => 2, 'priceRetail' => 800, 'price1' => 800, 'price2' => 800, 'priceOwn' => 800],
                        ],
                    ],
                ],
            ],
        ]
        )->seeJson(
        [
            'errors' => [
                'prices' => 'the Device Prices has not been created',
            ],
        ]);
    }

    public function testUpdateDevice()
    {
        $device = factory(\WA\DataStore\Device\Device::class)->create(
            ['properties' => 'properties1', 'name' => 'Phone1']
        );
        $deviceAux = factory(\WA\DataStore\Device\Device::class)->create(
            ['properties' => 'properties2', 'name' => 'Phone2']
        );

        $this->assertNotEquals($device->id, $deviceAux->id);
        $this->assertNotEquals($device->identification, $deviceAux->identification);
        $this->assertNotEquals($device->name, $deviceAux->name);
        $this->assertNotEquals($device->properties, $deviceAux->properties);

        $modCap1 = factory(\WA\DataStore\Modification\Modification::class)->create(
            ['modType' => 'capacity']
        );

        $modSty1 = factory(\WA\DataStore\Modification\Modification::class)->create(
            ['modType' => 'style']
        );

        $modCap2 = factory(\WA\DataStore\Modification\Modification::class)->create(
            ['modType' => 'capacity']
        );

        $price1 = factory(\WA\DataStore\Price\Price::class)->create(
            [
                'capacityId' => 1,
                'styleId' => 2,
                'carrierId' => 1,
                'companyId' => 1,
                'priceRetail' => 100,
                'price1' => 100,
                'price2' => 100,
                'priceOwn' => 100, ]
        );

        $price2 = factory(\WA\DataStore\Price\Price::class)->create(
            [
                'capacityId' => 1,
                'styleId' => 2,
                'carrierId' => 1,
                'companyId' => 2,
                'priceRetail' => 200,
                'price1' => 200,
                'price2' => 200,
                'priceOwn' => 200, ]
        );

        $price3 = factory(\WA\DataStore\Price\Price::class)->create(
            [
                'capacityId' => 1,
                'styleId' => 2,
                'carrierId' => 2,
                'companyId' => 1,
                'priceRetail' => 300,
                'price1' => 300,
                'price2' => 300,
                'priceOwn' => 300, ]
        );

        $price4 = factory(\WA\DataStore\Price\Price::class)->create(
            [
                'capacityId' => 1,
                'styleId' => 2,
                'carrierId' => 2,
                'companyId' => 2,
                'priceRetail' => 400,
                'price1' => 400,
                'price2' => 400,
                'priceOwn' => 400, ]
        );

        $price5 = factory(\WA\DataStore\Price\Price::class)->create(
            [
                'capacityId' => 3,
                'styleId' => 2,
                'carrierId' => 1,
                'companyId' => 1,
                'priceRetail' => 500,
                'price1' => 500,
                'price2' => 500,
                'priceOwn' => 500, ]
        );

        $price6 = factory(\WA\DataStore\Price\Price::class)->create(
            [
                'capacityId' => 3,
                'styleId' => 2,
                'carrierId' => 1,
                'companyId' => 2,
                'priceRetail' => 600,
                'price1' => 600,
                'price2' => 600,
                'priceOwn' => 600, ]
        );

        $price7 = factory(\WA\DataStore\Price\Price::class)->create(
            [
                'capacityId' => 3,
                'styleId' => 2,
                'carrierId' => 2,
                'companyId' => 1,
                'priceRetail' => 700,
                'price1' => 700,
                'price2' => 700,
                'priceOwn' => 700, ]
        );

        $price8 = factory(\WA\DataStore\Price\Price::class)->create(
            [
                'capacityId' => 3,
                'styleId' => 2,
                'carrierId' => 2,
                'companyId' => 2,
                'priceRetail' => 800,
                'price1' => 800,
                'price2' => 800,
                'priceOwn' => 800, ]
        );

        $this->put('/devices/'.$device->id,
            [
                'data' => [
                    'type' => 'devices',
                    'attributes' => [
                        'name' => $deviceAux->name,
                        'properties' => $deviceAux->properties,
                    ],
                    'relationships' => [
                        'assets' => [
                            'data' => [
                                ['type' => 'assets', 'id' => '1'],
                                ['type' => 'assets', 'id' => '2'],
                            ],
                        ],
                        'modifications' => [
                            'data' => [
                                ['type' => 'modifications', 'id' => $modCap1->id],
                                ['type' => 'modifications', 'id' => $modSty1->id],
                                ['type' => 'modifications', 'id' => $modCap2->id],
                            ],
                        ],
                        'carriers' => [
                            'data' => [
                                ['type' => 'carriers', 'id' => '1'],
                                ['type' => 'carriers', 'id' => '2'],
                            ],
                        ],
                        'companies' => [
                            'data' => [
                                ['type' => 'companies', 'id' => '1'],
                                ['type' => 'companies', 'id' => '2'],
                            ],
                        ],
                        'prices' => [
                            'data' => [
                                ['type' => 'prices', 'id' => $price1->id, 'capacityId' => 1, 'styleId' => 2, 'carrierId' => 1, 'companyId' => 1, 'priceRetail' => 1100, 'price1' => 1100, 'price2' => 1100, 'priceOwn' => 1100],
                                ['type' => 'prices', 'id' => $price2->id, 'capacityId' => 1, 'styleId' => 2, 'carrierId' => 1, 'companyId' => 2, 'priceRetail' => 1200, 'price1' => 1200, 'price2' => 1200, 'priceOwn' => 1200],
                                ['type' => 'prices', 'id' => $price3->id, 'capacityId' => 1, 'styleId' => 2, 'carrierId' => 2, 'companyId' => 1, 'priceRetail' => 1300, 'price1' => 1300, 'price2' => 1300, 'priceOwn' => 1300],
                                ['type' => 'prices', 'id' => $price4->id, 'capacityId' => 1, 'styleId' => 2, 'carrierId' => 2, 'companyId' => 2, 'priceRetail' => 1400, 'price1' => 1400, 'price2' => 1400, 'priceOwn' => 1400],
                                ['type' => 'prices', 'id' => $price5->id, 'capacityId' => 3, 'styleId' => 2, 'carrierId' => 1, 'companyId' => 1, 'priceRetail' => 1500, 'price1' => 1500, 'price2' => 1500, 'priceOwn' => 1500],
                                ['type' => 'prices', 'id' => $price6->id, 'capacityId' => 3, 'styleId' => 2, 'carrierId' => 1, 'companyId' => 2, 'priceRetail' => 1600, 'price1' => 1600, 'price2' => 1600, 'priceOwn' => 1600],
                                ['type' => 'prices', 'id' => $price7->id, 'capacityId' => 3, 'styleId' => 2, 'carrierId' => 2, 'companyId' => 1, 'priceRetail' => 1700, 'price1' => 1700, 'price2' => 1700, 'priceOwn' => 1700],
                                ['type' => 'prices', 'id' => $price8->id, 'capacityId' => 3, 'styleId' => 2, 'carrierId' => 2, 'companyId' => 2, 'priceRetail' => 1800, 'price1' => 1800, 'price2' => 1800, 'priceOwn' => 1800],
                            ],
                        ],
                    ],
                ],
            ])
            ->seeJson(
            [
                'type' => 'devices',
                'name' => $deviceAux->name,
                'properties' => $deviceAux->properties,
            ]);
    }

    public function testDeleteDeviceIfExists()
    {
        // CREATE & DELETE
        $device = factory(\WA\DataStore\Device\Device::class)->create();
        $responseDel = $this->call('DELETE', '/devices/'.$device->id);
        //dd($responseDel);
        $this->assertEquals(200, $responseDel->status());
        $responseGet = $this->call('GET', '/devices/'.$device->id);
        $this->assertEquals(404, $responseGet->status());
    }

    public function testDeleteDeviceIfNoExists()
    {
        // DELETE NO EXISTING.
        $responseDel = $this->call('DELETE', '/devices/1');
        $this->assertEquals(404, $responseDel->status());
    }

    public function testParseJsonToArray()
    {
        $array = array();
        $type = 'anytype';

        for ($i = 1; $i < 5; ++$i) {
            $arrayAux = array('type' => $type, 'id' => $i);
            array_push($array, $arrayAux);
        }

        $devicesController = app()->make('WA\Http\Controllers\DevicesController');
        $reflection = new \ReflectionClass($devicesController);
        $method = $reflection->getMethod('parseJsonToArray');
        $method->setAccessible(true);

        $value = $method->invokeArgs($devicesController, array($array, $type));
        $real = array(1, 2, 3, 4);
        $this->assertSame($value, $real);
    }

    public function testParseJsonToArrayReturnVoidNoType()
    {
        $array = array();
        $type = 'anytype';

        for ($i = 1; $i < 5; ++$i) {
            $arrayAux = array('error' => $type, 'id' => $i);
            array_push($array, $arrayAux);
        }

        $devicesController = app()->make('WA\Http\Controllers\DevicesController');
        $reflection = new \ReflectionClass($devicesController);
        $method = $reflection->getMethod('parseJsonToArray');
        $method->setAccessible(true);

        $value = $method->invokeArgs($devicesController, array($array, $type));
        $real = array();
        $this->assertSame($value, $real);
    }

    public function testParseJsonToArrayReturnVoidNoSameType()
    {
        $array = array();
        $type = 'anytype';

        for ($i = 1; $i < 5; ++$i) {
            $arrayAux = array('type' => 'error', 'id' => $i);
            array_push($array, $arrayAux);
        }

        $devicesController = app()->make('WA\Http\Controllers\DevicesController');
        $reflection = new \ReflectionClass($devicesController);
        $method = $reflection->getMethod('parseJsonToArray');
        $method->setAccessible(true);

        $value = $method->invokeArgs($devicesController, array($array, $type));
        $real = array();
        $this->assertSame($value, $real);
    }

    public function testParseJsonToArrayReturnVoidNoId()
    {
        $array = array();
        $type = 'anytype';

        for ($i = 1; $i < 5; ++$i) {
            $arrayAux = array('type' => $type, 'error' => $i);
            array_push($array, $arrayAux);
        }

        $devicesController = app()->make('WA\Http\Controllers\DevicesController');
        $reflection = new \ReflectionClass($devicesController);
        $method = $reflection->getMethod('parseJsonToArray');
        $method->setAccessible(true);

        $value = $method->invokeArgs($devicesController, array($array, $type));
        $real = array();
        $this->assertSame($value, $real);
    }

    public function testDeleteRepeat()
    {
        $start = array(
            ['type' => 'prices', 'capacityId' => 1, 'styleId' => 2, 'carrierId' => 1, 'companyId' => 1, 'priceRetail' => 100, 'price1' => 100, 'price2' => 100, 'priceOwn' => 100],
            ['type' => 'prices', 'capacityId' => 1, 'styleId' => 2, 'carrierId' => 1, 'companyId' => 1, 'priceRetail' => 200, 'price1' => 200, 'price2' => 200, 'priceOwn' => 200],
            ['type' => 'prices', 'capacityId' => 1, 'styleId' => 2, 'carrierId' => 2, 'companyId' => 1, 'priceRetail' => 300, 'price1' => 300, 'price2' => 300, 'priceOwn' => 300],
            ['type' => 'prices', 'capacityId' => 1, 'styleId' => 2, 'carrierId' => 2, 'companyId' => 1, 'priceRetail' => 400, 'price1' => 400, 'price2' => 400, 'priceOwn' => 400],
        );

        $final = array(
            ['type' => 'prices', 'capacityId' => 1, 'styleId' => 2, 'carrierId' => 1, 'companyId' => 1, 'priceRetail' => 100, 'price1' => 100, 'price2' => 100, 'priceOwn' => 100],
            ['type' => 'prices', 'capacityId' => 1, 'styleId' => 2, 'carrierId' => 2, 'companyId' => 1, 'priceRetail' => 300, 'price1' => 300, 'price2' => 300, 'priceOwn' => 300],
        );

        $devicesController = app()->make('WA\Http\Controllers\DevicesController');
        $reflection = new \ReflectionClass($devicesController);
        $method = $reflection->getMethod('deleteRepeat');
        $method->setAccessible(true);

        $result = $method->invokeArgs($devicesController, array($start));
        $this->assertSame($result, $final);
    }

    public function testDeleteRepeatDoingNothing()
    {
        $start = array(
            ['type' => 'prices', 'capacityId' => 1, 'styleId' => 2, 'carrierId' => 1, 'companyId' => 1, 'priceRetail' => 100, 'price1' => 100, 'price2' => 100, 'priceOwn' => 100],
            ['type' => 'prices', 'capacityId' => 1, 'styleId' => 2, 'carrierId' => 2, 'companyId' => 1, 'priceRetail' => 300, 'price1' => 300, 'price2' => 300, 'priceOwn' => 300],
        );

        $final = array(
            ['type' => 'prices', 'capacityId' => 1, 'styleId' => 2, 'carrierId' => 1, 'companyId' => 1, 'priceRetail' => 100, 'price1' => 100, 'price2' => 100, 'priceOwn' => 100],
            ['type' => 'prices', 'capacityId' => 1, 'styleId' => 2, 'carrierId' => 2, 'companyId' => 1, 'priceRetail' => 300, 'price1' => 300, 'price2' => 300, 'priceOwn' => 300],
        );

        $devicesController = app()->make('WA\Http\Controllers\DevicesController');
        $reflection = new \ReflectionClass($devicesController);
        $method = $reflection->getMethod('deleteRepeat');
        $method->setAccessible(true);

        $result = $method->invokeArgs($devicesController, array($start));
        $this->assertSame($result, $final);
    }

    public function testCheckIfPriceRowIsCorrect()
    {

        //$this->artisan('db:seed');

        $modCap1 = factory(\WA\DataStore\Modification\Modification::class)->create(['modType' => 'capacity'])->id;
        $modSty2 = factory(\WA\DataStore\Modification\Modification::class)->create(['modType' => 'style'])->id;
        $modCap3 = factory(\WA\DataStore\Modification\Modification::class)->create(['modType' => 'capacity'])->id;
        $modSty4 = factory(\WA\DataStore\Modification\Modification::class)->create(['modType' => 'style'])->id;
        $modCap5 = factory(\WA\DataStore\Modification\Modification::class)->create(['modType' => 'capacity'])->id;
        $modSty6 = factory(\WA\DataStore\Modification\Modification::class)->create(['modType' => 'style'])->id;

        $carr1 = factory(\WA\DataStore\Carrier\Carrier::class)->create()->id;
        $carr2 = factory(\WA\DataStore\Carrier\Carrier::class)->create()->id;
        $carr3 = factory(\WA\DataStore\Carrier\Carrier::class)->create()->id;

        $comp1 = factory(\WA\DataStore\Company\Company::class)->create()->id;
        $comp2 = factory(\WA\DataStore\Company\Company::class)->create()->id;
        $comp3 = factory(\WA\DataStore\Company\Company::class)->create()->id;

        $price = array(
            'type' => 'prices', 'capacityId' => $modCap1, 'styleId' => $modSty2, 'carrierId' => $carr1, 'companyId' => $comp1, 'priceRetail' => 100, 'price1' => 100, 'price2' => 100, 'priceOwn' => 100,
        );
        $modifications = array($modCap1, $modSty2, $modCap3, $modSty4, $modCap5, $modSty6);
        $carriers = array($carr1, $carr2, $carr3);
        $companies = array($comp1, $comp2, $comp3);

        $devicesController = app()->make('WA\Http\Controllers\DevicesController');
        $reflection = new \ReflectionClass($devicesController);
        $method = $reflection->getMethod('checkIfPriceRowIsCorrect');
        $method->setAccessible(true);

        $result = $method->invokeArgs($devicesController, array($price, $modifications, $carriers, $companies));
        $final = array('bool' => true, 'error' => 'No Error', 'id' => 0);
        $this->assertSame($result, $final);
    }

    public function testCheckIfPriceRowIsCorrectCapacityFails()
    {
        $modCap1 = factory(\WA\DataStore\Modification\Modification::class)->create(['modType' => 'capacity'])->id;
        $modSty2 = factory(\WA\DataStore\Modification\Modification::class)->create(['modType' => 'style'])->id;
        $modCap3 = factory(\WA\DataStore\Modification\Modification::class)->create(['modType' => 'capacity'])->id;
        $modSty4 = factory(\WA\DataStore\Modification\Modification::class)->create(['modType' => 'style'])->id;
        $modCap5 = factory(\WA\DataStore\Modification\Modification::class)->create(['modType' => 'capacity'])->id;
        $modSty6 = factory(\WA\DataStore\Modification\Modification::class)->create(['modType' => 'style'])->id;

        $carr1 = factory(\WA\DataStore\Carrier\Carrier::class)->create()->id;
        $carr2 = factory(\WA\DataStore\Carrier\Carrier::class)->create()->id;
        $carr3 = factory(\WA\DataStore\Carrier\Carrier::class)->create()->id;

        $comp1 = factory(\WA\DataStore\Company\Company::class)->create()->id;
        $comp2 = factory(\WA\DataStore\Company\Company::class)->create()->id;
        $comp3 = factory(\WA\DataStore\Company\Company::class)->create()->id;

        $price = array(
            'type' => 'prices', 'capacityId' => $modCap1, 'styleId' => $modSty2, 'carrierId' => $carr1, 'companyId' => $comp1, 'priceRetail' => 100, 'price1' => 100, 'price2' => 100, 'priceOwn' => 100,
        );
        $modifications = array($modSty2, $modCap3, $modSty4, $modCap5, $modSty6);
        $carriers = array($carr1, $carr2, $carr3);
        $companies = array($comp1, $comp2, $comp3);

        $devicesController = app()->make('WA\Http\Controllers\DevicesController');
        $reflection = new \ReflectionClass($devicesController);
        $method = $reflection->getMethod('checkIfPriceRowIsCorrect');
        $method->setAccessible(true);

        $result = $method->invokeArgs($devicesController, array($price, $modifications, $carriers, $companies));
        $final = array('bool' => false, 'error' => 'Capacity Not Found', 'id' => 1);
        $this->assertSame($result, $final);
    }

    public function testCheckIfPriceRowIsCorrectStyleFails()
    {
        $modCap1 = factory(\WA\DataStore\Modification\Modification::class)->create(['modType' => 'capacity'])->id;
        $modSty2 = factory(\WA\DataStore\Modification\Modification::class)->create(['modType' => 'style'])->id;
        $modCap3 = factory(\WA\DataStore\Modification\Modification::class)->create(['modType' => 'capacity'])->id;
        $modSty4 = factory(\WA\DataStore\Modification\Modification::class)->create(['modType' => 'style'])->id;
        $modCap5 = factory(\WA\DataStore\Modification\Modification::class)->create(['modType' => 'capacity'])->id;
        $modSty6 = factory(\WA\DataStore\Modification\Modification::class)->create(['modType' => 'style'])->id;

        $carr1 = factory(\WA\DataStore\Carrier\Carrier::class)->create()->id;
        $carr2 = factory(\WA\DataStore\Carrier\Carrier::class)->create()->id;
        $carr3 = factory(\WA\DataStore\Carrier\Carrier::class)->create()->id;

        $comp1 = factory(\WA\DataStore\Company\Company::class)->create()->id;
        $comp2 = factory(\WA\DataStore\Company\Company::class)->create()->id;
        $comp3 = factory(\WA\DataStore\Company\Company::class)->create()->id;

        $price = array(
            'type' => 'prices', 'capacityId' => $modCap1, 'styleId' => $modSty2, 'carrierId' => $carr1, 'companyId' => $comp1, 'priceRetail' => 100, 'price1' => 100, 'price2' => 100, 'priceOwn' => 100,
        );
        $modifications = array($modCap1, $modCap3, $modSty4, $modCap5, $modSty6);
        $carriers = array($carr1, $carr2, $carr3);
        $companies = array($comp1, $comp2, $comp3);

        $devicesController = app()->make('WA\Http\Controllers\DevicesController');
        $reflection = new \ReflectionClass($devicesController);
        $method = $reflection->getMethod('checkIfPriceRowIsCorrect');
        $method->setAccessible(true);

        $result = $method->invokeArgs($devicesController, array($price, $modifications, $carriers, $companies));
        $final = array('bool' => false, 'error' => 'Style Not Found', 'id' => 2);
        $this->assertSame($result, $final);
    }

    public function testCheckIfPriceRowIsCorrectCarrierFails()
    {
        $modCap1 = factory(\WA\DataStore\Modification\Modification::class)->create(['modType' => 'capacity'])->id;
        $modSty2 = factory(\WA\DataStore\Modification\Modification::class)->create(['modType' => 'style'])->id;
        $modCap3 = factory(\WA\DataStore\Modification\Modification::class)->create(['modType' => 'capacity'])->id;
        $modSty4 = factory(\WA\DataStore\Modification\Modification::class)->create(['modType' => 'style'])->id;
        $modCap5 = factory(\WA\DataStore\Modification\Modification::class)->create(['modType' => 'capacity'])->id;
        $modSty6 = factory(\WA\DataStore\Modification\Modification::class)->create(['modType' => 'style'])->id;

        $carr1 = factory(\WA\DataStore\Carrier\Carrier::class)->create()->id;
        $carr2 = factory(\WA\DataStore\Carrier\Carrier::class)->create()->id;
        $carr3 = factory(\WA\DataStore\Carrier\Carrier::class)->create()->id;

        $comp1 = factory(\WA\DataStore\Company\Company::class)->create()->id;
        $comp2 = factory(\WA\DataStore\Company\Company::class)->create()->id;
        $comp3 = factory(\WA\DataStore\Company\Company::class)->create()->id;

        $price = array(
            'type' => 'prices', 'capacityId' => $modCap1, 'styleId' => $modSty2, 'carrierId' => $carr1, 'companyId' => $comp1, 'priceRetail' => 100, 'price1' => 100, 'price2' => 100, 'priceOwn' => 100,
        );
        $modifications = array($modCap1, $modSty2, $modCap3, $modSty4, $modCap5, $modSty6);
        $carriers = array($carr2, $carr3);
        $companies = array($comp1, $comp2, $comp3);

        $devicesController = app()->make('WA\Http\Controllers\DevicesController');
        $reflection = new \ReflectionClass($devicesController);
        $method = $reflection->getMethod('checkIfPriceRowIsCorrect');
        $method->setAccessible(true);

        $result = $method->invokeArgs($devicesController, array($price, $modifications, $carriers, $companies));
        $final = array('bool' => false, 'error' => 'Carrier Not Found', 'id' => 1);
        $this->assertSame($result, $final);
    }

    public function testCheckIfPriceRowIsCorrectCompanyFails()
    {
        $modCap1 = factory(\WA\DataStore\Modification\Modification::class)->create(['modType' => 'capacity'])->id;
        $modSty2 = factory(\WA\DataStore\Modification\Modification::class)->create(['modType' => 'style'])->id;
        $modCap3 = factory(\WA\DataStore\Modification\Modification::class)->create(['modType' => 'capacity'])->id;
        $modSty4 = factory(\WA\DataStore\Modification\Modification::class)->create(['modType' => 'style'])->id;
        $modCap5 = factory(\WA\DataStore\Modification\Modification::class)->create(['modType' => 'capacity'])->id;
        $modSty6 = factory(\WA\DataStore\Modification\Modification::class)->create(['modType' => 'style'])->id;

        $carr1 = factory(\WA\DataStore\Carrier\Carrier::class)->create()->id;
        $carr2 = factory(\WA\DataStore\Carrier\Carrier::class)->create()->id;
        $carr3 = factory(\WA\DataStore\Carrier\Carrier::class)->create()->id;

        $comp1 = factory(\WA\DataStore\Company\Company::class)->create()->id;
        $comp2 = factory(\WA\DataStore\Company\Company::class)->create()->id;
        $comp3 = factory(\WA\DataStore\Company\Company::class)->create()->id;

        $price = array(
            'type' => 'prices', 'capacityId' => $modCap1, 'styleId' => $modSty2, 'carrierId' => $carr1, 'companyId' => $comp1, 'priceRetail' => 100, 'price1' => 100, 'price2' => 100, 'priceOwn' => 100,
        );
        $modifications = array($modCap1, $modSty2, $modCap3, $modSty4, $modCap5, $modSty6);
        $carriers = array($carr1, $carr2, $carr3);
        $companies = array($comp2, $comp3);

        $devicesController = app()->make('WA\Http\Controllers\DevicesController');
        $reflection = new \ReflectionClass($devicesController);
        $method = $reflection->getMethod('checkIfPriceRowIsCorrect');
        $method->setAccessible(true);

        $result = $method->invokeArgs($devicesController, array($price, $modifications, $carriers, $companies));
        $final = array('bool' => false, 'error' => 'Company Not Found', 'id' => 1);
        $this->assertSame($result, $final);
    }

    /*
     *      Transforms an Object and gets the value of the Response.
     *
     *      @param:
     *          Object $info
     *      @return:
     *          $info->response->getContent()->data
     */
    private function getProtectedData($info)
    {
        try {
            $reflectorResponse = new \ReflectionClass($info);
            $classResponse = $reflectorResponse->getProperty('response');
            $classResponse->setAccessible(true);
            $dataResponse = $classResponse->getValue($info);

            return json_decode($dataResponse->getContent())->data;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getProtectedId($info)
    {
        try {
            $reflectorResponse = new \ReflectionClass($info);
            $classResponse = $reflectorResponse->getProperty('response');
            $classResponse->setAccessible(true);
            $dataResponse = $classResponse->getValue($info);

            return json_decode($dataResponse->getContent())->data->id;
        } catch (\Exception $e) {
            return 1;
        }
    }

    private function getProtectedIdfromDevice($info)
    {
        try {
            $reflectorResponse = new \ReflectionClass($info);
            $classResponse = $reflectorResponse->getProperty('attributes');
            $classResponse->setAccessible(true);
            $dataResponse = $classResponse->getValue($info);

            return $dataResponse['id'];
        } catch (\Exception $e) {
            return 1;
        }
    }
}

/*
             *  @TODO: Code that gets the id of devices and tries to do a get with include
             *
             *
            $reflectorResponse = new \ReflectionClass($device);
            $classResponse = $reflectorResponse->getProperty('response');
            $classResponse->setAccessible(true);
            $dataResponse = $classResponse->getValue($device);

            $reflectorContent = new \ReflectionClass($dataResponse);
            $classContent = $reflectorContent->getProperty('content');
            $classContent->setAccessible(true);
            $dataContent = $classContent->getValue($dataResponse);

            $json = json_decode($dataContent);

            $assets = $this->get('/devices/'.$json->data->id.'?include=assets')
                ->seeJsonStructure([
                    'included' => [
                        'type' => 'assets'
                    ]
                ]);


// EXAMPLE POST DEVICE
{
    "data" : {
        "type" : "devices",
        "attributes" : {
            "name" : "nameDevice",
            "properties" : "propertiesDevice",
            "deviceTypeId" : 1,
            "statusId" : 1,
            "externalId" : 1,
            "identification" : 123456789,
            "syncId" : 1
        },
        "relationships" : {

            "images" : {
                "data" : [
                    { "type": "images", "id" : 1 },
                    { "type": "images", "id" : 2 }
                ]
            },

            "assets" : {
                "data" : [
                    { "type": "assets", "id" : 1 },
                    { "type": "assets", "id" : 2 }
                ]
            },
            "modifications" : {
                "data" : [
                    { "type": "modifications", "id" : 1 },
                    { "type": "modifications", "id" : 2 },
                    { "type": "modifications", "id" : 3 }
                ]
            },
            "carriers" : {
                "data" : [
                    { "type": "carriers", "id" : 1 },
                    { "type": "carriers", "id" : 2 }
                ]
            },
            "companies" : {
                "data" : [
                    { "type": "companies", "id" : 1 },
                    { "type": "companies", "id" : 2 }
                ]
            },
            "prices" : {
                "data" : [
                    {
                        "type": "prices",
                        "capacityId": 1,
                        "styleId": 2,
                        "carrierId": 1,
                        "companyId": 1,
                        "priceRetail": 100,
                        "price1": 100,
                        "price2": 100,
                        "priceOwn": 100
                    },
                    {
                        "type": "prices",
                        "capacityId": 1,
                        "styleId": 2,
                        "carrierId": 1,
                        "companyId": 2,
                        "priceRetail": 200,
                        "price1": 200,
                        "price2": 200,
                        "priceOwn": 200
                    },
                    {
                        "type": "prices",
                        "capacityId": 1,
                        "styleId": 2,
                        "carrierId": 2,
                        "companyId": 1,
                        "priceRetail": 300,
                        "price1": 300,
                        "price2": 300,
                        "priceOwn": 300
                    },
                    {
                        "type": "prices",
                        "capacityId": 1,
                        "styleId": 2,
                        "carrierId": 2,
                        "companyId": 2,
                        "priceRetail": 400,
                        "price1": 400,
                        "price2": 400,
                        "priceOwn": 400
                    },
                    {
                        "type": "prices",
                        "capacityId": 3,
                        "styleId": 2,
                        "carrierId": 1,
                        "companyId": 1,
                        "priceRetail": 500,
                        "price1": 500,
                        "price2": 500,
                        "priceOwn": 500
                    },
                    {
                        "type": "prices",
                        "capacityId": 3,
                        "styleId": 2,
                        "carrierId": 1,
                        "companyId": 2,
                        "priceRetail": 600,
                        "price1": 600,
                        "price2": 600,
                        "priceOwn": 600
                    },
                    {
                        "type": "prices",
                        "capacityId": 3,
                        "styleId": 2,
                        "carrierId": 2,
                        "companyId": 1,
                        "priceRetail": 700,
                        "price1": 700,
                        "price2": 700,
                        "priceOwn": 700
                    },
                    {
                        "type": "prices",
                        "capacityId": 3,
                        "styleId": 2,
                        "carrierId": 2,
                        "companyId": 2,
                        "priceRetail": 800,
                        "price1": 800,
                        "price2": 800,
                        "priceOwn": 800
                    }
                ]
            }
        }
    }
}




        $id = $this->getProtectedId($device);

        $assets = $this->json('GET', 'devices/'.$id.'?include=assets')
            ->seeJsonStructure([
                'data' => [
                    'type',
                    'id',
                    'attributes' => [
                        'identification',
                        'name',
                        'properties',
                        'externalId',
                        'deviceTypeId',
                        'statusId',
                        'syncId',
                        'created_at' => [
                            'date',
                            'timezone_type',
                            'timezone'
                        ],
                        'updated_at' => [
                            'date',
                            'timezone_type',
                            'timezone'
                        ]
                    ],
                    'links' => [
                        'self'
                    ],
                    'relationships' => [
                        'assets' => [
                            'links' => [
                                'self',
                                'related'
                            ],
                            'data' => [
                                0 => [
                                    'type',
                                    'id'
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
                            'identification',
                            'active',
                            'statusId',
                            'typeId',
                            'externalId',
                            'carrierId',
                            'syncId'
                        ],
                        'links' => [
                            'self'
                        ]
                    ]

                ]
            ]);
*/
