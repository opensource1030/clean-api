<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use WA\DataStore\Condition\Condition;

class PackageApiTest extends TestCase
{
    use DatabaseMigrations;

    public function testGetPackages()
    {
        factory(\WA\DataStore\Package\Package::class, 40)->create();

        $res = $this->json('GET', 'packages');

        $res->seeJsonStructure([
            'data' => [
                0 => [
                    'type',
                    'id',
                    'attributes' => [
                        'name',
                        'companyId',
                        'addressId',
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
                'sort',
                'filter',
                'fields',
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

    public function testGetPackageByIdIfExists()
    {
        $package = factory(\WA\DataStore\Package\Package::class)->create();

        $res = $this->json('GET', 'packages/'.$package->id)
            ->seeJson([
                'type' => 'packages',
                'name' => $package->name,
            ]);

        $res->seeJsonStructure([
            'data' => [
                'type',
                'id',
                'attributes' => [
                    'name',
                    'addressId',
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

    public function testGetPackageByIdIfNoExists()
    {
        $packageId = factory(\WA\DataStore\Package\Package::class)->create()->id;
        $packageId = $packageId + 10;

        $response = $this->call('GET', '/packages/'.$packageId);
        $this->assertEquals(404, $response->status());
    }

    public function testGetDeviceByIdandIncludesConditions()
    {
        $package = factory(\WA\DataStore\Package\Package::class)->create();

        $condition1 = factory(\WA\DataStore\Condition\Condition::class)->create()->id;
        $condition2 = factory(\WA\DataStore\Condition\Condition::class)->create()->id;

        $dataConditions = array($condition1, $condition2);

        $package->conditions()->sync($dataConditions);

        $this->json('GET', 'packages/'.$package->id.'?include=conditions')
            ->seeJsonStructure([
                'data' => [
                    'type',
                    'id',
                    'attributes' => [
                        'name',
                        'companyId',
                        'addressId',
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
                        'conditions' => [
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
                    0 => [
                        'type',
                        'id',
                        'attributes' => [
                            'typeCond',
                            'name',
                            'condition',
                            'value',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],

                ],
            ]);
    }

    public function testGetPackageByIdandIncludesServices()
    {
        $this->markTestSkipped('Test for older version');

        $package = factory(\WA\DataStore\Package\Package::class)->create();

        $service1 = factory(\WA\DataStore\Service\Service::class)->create()->id;
        $service2 = factory(\WA\DataStore\Service\Service::class)->create()->id;

        $dataServices = array($service1, $service2);

        $package->services()->sync($dataServices);

        $res = $this->json('GET', 'packages/'.$package->id.'?include=services')
            ->seeJsonStructure([
                'data' => [
                    'type',
                    'id',
                    'attributes' => [
                        'name',
                        'companyId',
                        'addressId',
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
                    ],
                ],
                'included' => [
                    0 => [
                        'type',
                        'id',
                        'attributes' => [
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
                        ],
                        'links' => [
                            'self',
                        ],
                    ],

                ],
            ]);
    }
/*  @TODO: DeviceVariations
    public function testGetPackageByIdandIncludesDevices()
    {
        $package = factory(\WA\DataStore\Package\Package::class)->create();

        //$device = factory(\WA\DataStore\Device\Device::class)->create()->id;

        //$package->devices()->sync(array($device));

        $res = $this->json('GET', 'packages/'.$package->id.'?include=devices')
            ->seeJsonStructure([
                'data' => [
                    'type',
                    'id',
                    'attributes' => [
                        'name',
                        'companyId',
                        'addressId',
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
                    ],
                ],
                'included' => [
                    1 => [
                        'type',
                        'id',
                        'attributes' => [
                            'name',
                            'properties',
                            'statusId',
                            'externalId',
                            'identification',
                            'syncId',
                        ],
                        'links' => [
                            'self',
                        ],
                        'relationships' => [
                            'devicetypes' => [
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

                ],
            ]);
    }
*/
    public function testGetPackageByIdandIncludesApps()
    {
        $package = factory(\WA\DataStore\Package\Package::class)->create();

        $app1 = factory(\WA\DataStore\App\App::class)->create()->id;
        $app2 = factory(\WA\DataStore\App\App::class)->create()->id;

        $dataApps = array($app1, $app2);

        $package->apps()->sync($dataApps);

        $this->json('GET', 'packages/'.$package->id.'?include=apps')
            ->seeJsonStructure([
                'data' => [
                    'type',
                    'id',
                    'attributes' => [
                        'name',
                        'companyId',
                        'addressId',
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
                            ],
                        ],
                    ],
                ],
                'included' => [
                    0 => [
                        'type',
                        'id',
                        'attributes' => [
                            'type',
                            'image',
                            'description',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],

                ],
            ]);
    }

    public function testCreatePackage()
    {
        $package = factory(\WA\DataStore\Package\Package::class)->create();

        $companyId = factory(\WA\DataStore\Company\Company::class)->create()->id;

        $condition1 = factory(\WA\DataStore\Condition\Condition::class)->create()->id;
        $condition2 = factory(\WA\DataStore\Condition\Condition::class)->create()->id;

        $service1 = factory(\WA\DataStore\Service\Service::class)->create()->id;
        $service2 = factory(\WA\DataStore\Service\Service::class)->create()->id;

        $device1 = factory(\WA\DataStore\Device\Device::class)->create()->id;
        $device2 = factory(\WA\DataStore\Device\Device::class)->create()->id;
        $device3 = factory(\WA\DataStore\Device\Device::class)->create()->id;

        $app1 = factory(\WA\DataStore\App\App::class)->create()->id;
        $app2 = factory(\WA\DataStore\App\App::class)->create()->id;

        $this->json('POST', 'packages',
            [
                "data" => [
                    "type" => "packages",
                    "attributes" => [
                        "name" => "namePackage",
                        "companyId" => $companyId
                    ],
                    'relationships' => [
                        'conditions' => [
                            'data' => [
                                ['type' => 'conditions', 'id' => $condition1],
                                ['type' => 'conditions', 'id' => $condition2],
                            ],
                        ],
                        'services' => [
                            'data' => [
                                ['type' => 'services', 'id' => $service1],
                                ['type' => 'services', 'id' => $service2],
                            ],
                        ],
                        'devices' => [
                            'data' => [
                                ['type' => 'devices', 'id' => $device1],
                                ['type' => 'devices', 'id' => $device2],
                                ['type' => 'devices', 'id' => $device3],
                            ],
                        ],
                        'apps' => [
                            'data' => [
                                ['type' => 'apps', 'id' => $app1],
                                ['type' => 'apps', 'id' => $app2],
                            ],
                        ],
                    ],
                ],
            ]
            )->seeJson(
            [
                'type' => 'packages',
                'name' => 'namePackage',
            ]);
    }

    public function testCreatePackageReturnNoValidData()
    {
        // 'data' no valid.
        $package = $this->json('POST', 'packages',
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

    public function testCreatePackageReturnNoValidType()
    {
        // 'type' no valid.
        $package = $this->json('POST', 'packages',
            [
                'data' => [
                    'NoValid' => 'packages',
                    'attributes' => [
                        'name' => 'namePackage',
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

    public function testCreatePackageReturnNoValidAttributes()
    {
        // 'attributes' no valid.
        $package = $this->json('POST', 'packages',
            [
                'data' => [
                    'type' => 'packages',
                    'NoValid' => [
                        'name' => 'namePackage',
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

    public function testCreatePackageReturnRelationshipNoExists()
    {
        $address = factory(WA\DataStore\Address\Address::class)->create()->id;
        $companyId = factory(\WA\DataStore\Company\Company::class)->create()->id;

        $package = $this->json('POST', 'packages',
        [
            'data' => [
                'type' => 'packages',
                'attributes' => [
                    'name' => 'namePackage',
                    'addressId' => $address,
                    'companyId' => $companyId
                ],
                'relationships' => [
                    'IgnoreType' => [
                        'data' => [
                            ['type' => 'apps', 'id' => '1'],
                            ['type' => 'apps', 'id' => '2'],
                        ],
                    ],
                ],
            ],
        ]
        )->seeJson(
        [
            'type' => 'packages',
            'name' => 'namePackage',
            'addressId' => $address,
        ]);
    }

    public function testCreatePackageReturnRelationshipNoExistsData()
    {
        $address = factory(WA\DataStore\Address\Address::class)->create()->id;
        $companyId = factory(\WA\DataStore\Company\Company::class)->create()->id;

        $package = $this->json('POST', 'packages',
        [
            'data' => [
                'type' => 'packages',
                'attributes' => [
                    'name' => 'namePackage',
                    'addressId' => $address,
                    'companyId' => $companyId
                ],
                'relationships' => [
                    'apps' => [
                        'IgnoreData' => [
                            ['type' => 'apps', 'id' => '1'],
                            ['type' => 'apps', 'id' => '2'],
                        ],
                    ],
                ],
            ],
        ]
        )->seeJson(
        [
            'type' => 'packages',
            'name' => 'namePackage',
            'addressId' => $address,
        ]);
    }

    public function testCreatePackageReturnRelationshipNoAppsType()
    {
        $address = factory(WA\DataStore\Address\Address::class)->create()->id;
        $companyId = factory(\WA\DataStore\Company\Company::class)->create()->id;

        $package = $this->json('POST', 'packages',
        [
            'data' => [
                'type' => 'packages',
                'attributes' => [
                    'name' => 'namePackage',
                    'addressId' => $address,
                    'companyId' => $companyId
                ],
                'relationships' => [
                    'apps' => [
                        'data' => [
                            ['type' => 'NoApps', 'id' => '1'],
                            ['type' => 'NoApps', 'id' => '2'],
                        ],
                    ],
                ],
            ],
        ]
        )->seeJson(
        [
            'type' => 'packages',
            'name' => 'namePackage',
            'addressId' => $address,
        ]);
    }

    public function testCreatePackageReturnRelationshipNoIdExists()
    {
        $address = factory(WA\DataStore\Address\Address::class)->create()->id;
        $companyId = factory(\WA\DataStore\Company\Company::class)->create()->id;

        $package = $this->json('POST', 'packages',
        [
            'data' => [
                'type' => 'packages',
                'attributes' => [
                    'name' => 'namePackage',
                    'addressId' => $address,
                    'companyId' => $companyId
                ],
                'relationships' => [
                    'apps' => [
                        'data' => [
                            ['type' => 'apps', 'aa' => '1'],
                            ['type' => 'apps', 'aa' => '2'],
                        ],
                    ],
                ],
            ],
        ]
        )->seeJson(
        [
            'type' => 'packages',
            'name' => 'namePackage',
            'addressId' => $address,
        ]);
    }

    public function testUpdatePackage()
    {
        $package = factory(\WA\DataStore\Package\Package::class)->create(
            ['name' => 'namePackage1']
        );
        $packageAux = factory(\WA\DataStore\Package\Package::class)->create(
            ['name' => 'namePackage2']
        );

        $this->assertNotEquals($package->id, $packageAux->id);
        $this->assertNotEquals($package->name, $packageAux->name);
        $this->assertNotEquals($package->addressId, $packageAux->addressId);

        $this->PATCH('/packages/'.$package->id,
            [
                'data' => [
                    'type' => 'packages',
                    'attributes' => [
                        'name' => $packageAux->name,
                        'addressId' => $packageAux->addressId,
                    ],
                ],
            ])
            ->seeJson(
            [
                'type' => 'packages',
                'name' => $packageAux->name,
                'addressId' => $packageAux->addressId,
            ]);
    }

    public function testDeletePackageIfExists()
    {
        // CREATE & DELETE
        $package = factory(\WA\DataStore\Package\Package::class)->create();
        $responseDel = $this->call('DELETE', '/packages/'.$package->id);
        $this->assertEquals(200, $responseDel->status());
        $responseGet = $this->call('GET', '/packages/'.$package->id);
        $this->assertEquals(404, $responseGet->status());
    }

    public function testDeletePackageIfNoExists()
    {
        // DELETE NO EXISTING.
        $responseDel = $this->call('DELETE', '/packages/1');
        $this->assertEquals(404, $responseDel->status());
    }
}
