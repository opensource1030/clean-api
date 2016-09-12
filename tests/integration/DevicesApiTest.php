<?php

use Laravel\Lumen\Testing\DatabaseTransactions;

class DevicesApiTest extends TestCase
{

    use DatabaseTransactions;


    public function testGetDevices() {
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
                    ]
                ]
            ],
            'meta' => [
                'pagination' => [
                    'total',
                    'count',
                    'per_page',
                    'current_page',
                    'total_pages'
                ]
            ],
            'links' => [
                'self',
                'first',
                'next',
                'last'
            ]
        ]);
    }

    public function testGetDeviceByIdIfExists() {
        // CREATE & GET
        $device = factory(\WA\DataStore\Device\Device::class)->create();

        $res = $this->json('GET', 'devices/'.$device->id)
            ->seeJson([
                'type' => 'devices',
                'identification'=> $device->identification,
                'name'=> $device->name,
                'properties'=> $device->properties,
                'externalId'=> $device->externalId,
                'deviceTypeId'=> $device->deviceTypeId,
                'statusId'=> $device->statusId,
                'syncId'=> $device->syncId
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
                ]
            ]
        ]);
    }

    public function testGetDeviceByIdIfNoExists() {
        // GET NO CREATED
        $response = $this->call('GET', '/devices/1000000');
        $this->assertEquals(409, $response->status());
    }

    public function testCreateDevice() {
        $device = $this->post('/devices',
            [
                'data' => [
                    'type' => 'devices',
                    'attributes' => [
                        'name' => 'whenIneedMotivation...',
                        'properties' => 'MyOneSolutionIsMyQueen',
                        'deviceTypeId'  => 1,
                        'statusId' => 1,
                        'externalId' => 1,
                        'identification' => rand(9000000000000,9999999999999)
                    ],
                    'relationships' => [
                        'images' => [
                            'data' => [
                                [ 'type' => 'images', 'id' => '1' ],
                                [ 'type' => 'images', 'id' => '2' ]
                            ]
                        ],
                        'assets' => [
                            'data' => [
                                [ 'type' => 'assets', 'id' => '1' ],
                                [ 'type' => 'assets', 'id' => '2' ]
                            ]
                        ],
                        'modifications' => [
                            'data' => [
                                [ 'type' => 'modifications', 'id' => '1' ],
                                [ 'type' => 'modifications', 'id' => '2' ],
                                [ 'type' => 'modifications', 'id' => '3' ]
                            ]
                        ],
                        'carriers' => [
                            'data' => [
                                [ 'type' => 'carriers', 'id' => '1' ],
                                [ 'type' => 'carriers', 'id' => '2' ]
                            ]
                        ],
                        'companies' => [
                            'data' => [
                                [ 'type' => 'companies', 'id' => '1' ],
                                [ 'type' => 'companies', 'id' => '2' ]
                            ]
                        ],
                        'prices' => [
                            'data' => [
                                [ 'type' => 'prices', 'capacityId' => 1, 'styleId' => 2, 'carrierId' => 1, 'companyId' => 1, 'priceRetail' => 100, 'price1' => 100, 'price2' => 100, 'priceOwn' => 100 ],
                                [ 'type' => 'prices', 'capacityId' => 1, 'styleId' => 2, 'carrierId' => 1, 'companyId' => 2, 'priceRetail' => 200, 'price1' => 200, 'price2' => 200, 'priceOwn' => 200 ],
                                [ 'type' => 'prices', 'capacityId' => 1, 'styleId' => 2, 'carrierId' => 2, 'companyId' => 1, 'priceRetail' => 300, 'price1' => 300, 'price2' => 300, 'priceOwn' => 300 ],
                                [ 'type' => 'prices', 'capacityId' => 1, 'styleId' => 2, 'carrierId' => 2, 'companyId' => 2, 'priceRetail' => 400, 'price1' => 400, 'price2' => 400, 'priceOwn' => 400 ],
                                [ 'type' => 'prices', 'capacityId' => 3, 'styleId' => 2, 'carrierId' => 1, 'companyId' => 1, 'priceRetail' => 500, 'price1' => 500, 'price2' => 500, 'priceOwn' => 500 ],
                                [ 'type' => 'prices', 'capacityId' => 3, 'styleId' => 2, 'carrierId' => 1, 'companyId' => 2, 'priceRetail' => 600, 'price1' => 600, 'price2' => 600, 'priceOwn' => 600 ],
                                [ 'type' => 'prices', 'capacityId' => 3, 'styleId' => 2, 'carrierId' => 2, 'companyId' => 1, 'priceRetail' => 700, 'price1' => 700, 'price2' => 700, 'priceOwn' => 700 ],
                                [ 'type' => 'prices', 'capacityId' => 3, 'styleId' => 2, 'carrierId' => 2, 'companyId' => 2, 'priceRetail' => 800, 'price1' => 800, 'price2' => 800, 'priceOwn' => 800 ]
                            ]
                        ]
                    ]
                ]
            ]
            )->seeJson(
            [
                'type' => 'devices',
                'name' => 'whenIneedMotivation...',
                'properties' => 'MyOneSolutionIsMyQueen',
                'deviceTypeId'  => 1,
                'statusId' => 1,
                'externalId' => 1
            ]);
    }

    public function testCreateDeviceReturnNoValidData() {
        // 'data' no valid.
        $device = $this->post('/devices',
            [
                'NoValid' => [
                    ]
            ]
            )->seeJson(
            [
                'errors' => [
                    'json' => 'Json is Invalid'
                ]
            ]
        );
    }

    public function testCreateDeviceReturnNoValidType() {
        // 'type' no valid.
        $device = $this->post('/devices',
            [
                "data" => [
                    "NoValid"=> "devices",
                    "attributes"=> [
                        "name"=> "whenIneedMotivation...",
                        "properties"=> "MyOneSolutionIsMyQueen",
                        "deviceTypeId" => 5
                    ]
                ]
                
            ]
            )->seeJson(
            [
                'errors' => [
                    'json' => 'Json is Invalid'
                ]
            ]
        );
    }

    public function testCreateDeviceReturnNoValidAttributes() {
        // 'attributes' no valid.
        $device = $this->post('/devices',
            [
                "data" => [
                    "type"=> "devices",
                    "NoValid"=> [
                        "name"=> "whenIneedMotivation...",
                        "properties"=> "MyOneSolutionIsMyQueen",
                        "deviceTypeId" => 5
                    ]
                ]
            ]
            )->seeJson(
            [
                'errors' => [
                    'json' => 'Json is Invalid'
                ]
            ]
        );
    }

    public function testCreateDeviceReturnNoValidDeviceTypeId() {
        // deviceTyoeId integrity foreign key error.
        $device = $this->post('/devices',
            [
                "data" => [
                    "type"=> "devices",
                    "attributes"=> [
                        "name"=> "whenIneedMotivation...",
                        "properties"=> "MyOneSolutionIsMyQueen",
                        "deviceTypeId" => 1000
                    ]
                ]
            ]
            )->seeJson(
            [
                'errors' => [
                    'devices' => 'The Device can not be created'
                ]
            ]
        );
    }

/*
    public function testCreateDeviceReturnRelationshipNoExists() {
        $device = $this->post('/devices',
        [
            'data' => [
                'type' => 'devices',
                'attributes' => [
                    'identification' => rand(9000000000000,9999999999999),
                    'name' => 'whenIneedMotivation...',
                    'properties' => 'MyOneSolutionIsMyQueen',
                    'deviceTypeId'  => 5
                ],
                'relationships' => [
                    'IgnoreType' => [
                        'data' => [
                            [ 'type' => 'assets', 'id' => '1' ],
                            [ 'type' => 'assets', 'id' => '2' ]
                        ]
                    ]
                ]
            ]
        ]
        )->seeJson(
        [
            'type' => 'devices',
            'name' => 'whenIneedMotivation...',
            'properties' => 'MyOneSolutionIsMyQueen',
            'deviceTypeId'  => 5
        ]);
    }

    public function testCreateDeviceReturnRelationshipNoExistsData() {

        $device = $this->post('/devices',
        [
            'data' => [
                'type' => 'devices',
                'attributes' => [
                    'identification' => rand(9000000000000,9999999999999),
                    'name' => 'whenIneedMotivation...',
                    'properties' => 'MyOneSolutionIsMyQueen',
                    'deviceTypeId'  => 5
                ],
                'relationships' => [
                    'assets' => [
                        'IgnoreType' => [
                            [ 'type' => 'assets', 'id' => '1' ],
                            [ 'type' => 'assets', 'id' => '2' ]
                        ]
                    ]
                ]
            ]
        ]
        )->seeJson(
        [
            'type' => 'devices',
            'name' => 'whenIneedMotivation...',
            'properties' => 'MyOneSolutionIsMyQueen',
            'deviceTypeId'  => 5
        ]);
    }

    public function testCreateDeviceReturnRelationshipNoAssetsType() {

        $device = $this->post('/devices',
        [
            'data' => [
                'type' => 'devices',
                'attributes' => [
                    'identification' => rand(9000000000000,9999999999999),
                    'name' => 'whenIneedMotivation...',
                    'properties' => 'MyOneSolutionIsMyQueen',
                    'deviceTypeId'  => 5
                ],
                'relationships' => [
                    'assets' => [
                        'data' => [
                            [ 'type' => 'NoAssets', 'id' => '1' ],
                            [ 'type' => 'NoAssets', 'id' => '2' ]
                        ]
                    ]
                ]
            ]
        ]
        )->seeJson(
        [
            'type' => 'devices',
            'name' => 'whenIneedMotivation...',
            'properties' => 'MyOneSolutionIsMyQueen',
            'deviceTypeId'  => 5
        ]);
    }

    public function testCreateDeviceReturnRelationshipNoIdExists() {

        $device = $this->post('/devices',
        [
            'data' => [
                'type' => 'devices',
                'attributes' => [
                    'identification' => rand(9000000000000,9999999999999),
                    'name' => 'whenIneedMotivation...',
                    'properties' => 'MyOneSolutionIsMyQueen',
                    'deviceTypeId'  => 5
                ],
                'relationships' => [
                    'assets' => [
                        'data' => [
                            [ 'type' => 'assets', 'aa' => '1' ],
                            [ 'type' => 'assets', 'aa' => '2' ]
                        ]
                    ]
                ]
            ]
        ]
        )->seeJson(
        [
            'type' => 'devices',
            'name' => 'whenIneedMotivation...',
            'properties' => 'MyOneSolutionIsMyQueen',
            'deviceTypeId'  => 5
        ]);
    }
*/
    public function testCreateDeviceReturnPriceModificationCapacityForeignKeyError() {

        $device = $this->post('/devices',
        [
            'data' => [
                'type' => 'devices',
                'attributes' => [
                    'identification' => rand(9000000000000,9999999999999),
                    'name' => 'whenIneedMotivation...',
                    'properties' => 'MyOneSolutionIsMyQueen',
                    'deviceTypeId'  => 5
                ],
                "relationships" => [
                    "modifications" => [
                        "data" => [
                            [ "type" => "modifications", "id" => 0 ],
                            [ "type" => "modifications", "id" => 2 ],
                            [ "type" => "modifications", "id" => 3 ]
                        ]
                    ],
                    "carriers" => [
                        "data" => [
                            [ "type" => "carriers", "id" => 1 ],
                            [ "type" => "carriers", "id" => 2 ]
                        ]
                    ],
                    "companies" => [
                        "data" => [
                            [ "type" => "companies", "id" => 1 ],
                            [ "type" => "companies", "id" => 2 ]
                        ]
                    ],
                    "prices" => [
                        "data" => [
                            [ "type" => "prices", "capacityId" => 1, "styleId" => 2, "carrierId" => 1, "companyId" => 1, "priceRetail" => 100, "price1" => 100, "price2" => 100, "priceOwn" => 100 ],
                            [ "type" => "prices", "capacityId" => 1, "styleId" => 2, "carrierId" => 1, "companyId" => 2, "priceRetail" => 200, "price1" => 200, "price2" => 200, "priceOwn" => 200 ],
                            [ "type" => "prices", "capacityId" => 1, "styleId" => 2, "carrierId" => 2, "companyId" => 1, "priceRetail" => 300, "price1" => 300, "price2" => 300, "priceOwn" => 300 ],
                            [ "type" => "prices", "capacityId" => 1, "styleId" => 2, "carrierId" => 2, "companyId" => 2, "priceRetail" => 400, "price1" => 400, "price2" => 400, "priceOwn" => 400 ],
                            [ "type" => "prices", "capacityId" => 3, "styleId" => 2, "carrierId" => 1, "companyId" => 1, "priceRetail" => 500, "price1" => 500, "price2" => 500, "priceOwn" => 500 ],
                            [ "type" => "prices", "capacityId" => 3, "styleId" => 2, "carrierId" => 1, "companyId" => 2, "priceRetail" => 600, "price1" => 600, "price2" => 600, "priceOwn" => 600 ],
                            [ "type" => "prices", "capacityId" => 3, "styleId" => 2, "carrierId" => 2, "companyId" => 1, "priceRetail" => 700, "price1" => 700, "price2" => 700, "priceOwn" => 700 ],
                            [ "type" => "prices", "capacityId" => 3, "styleId" => 2, "carrierId" => 2, "companyId" => 2, "priceRetail" => 800, "price1" => 800, "price2" => 800, "priceOwn" => 800 ]
                        ]
                    ]
                ]
            ]
        ]
        )->seeJson(
        [
            'errors' => [
                'modifications' => 'the Device Modifications can not be created',
                'prices' => 'the Device Prices can not be created because other relationships can\'t be created'
            ]
        ]);
    }

    public function testCreateDeviceReturnPriceModificationStyleForeignKeyError() {

        $device = $this->post('/devices',
        [
            'data' => [
                'type' => 'devices',
                'attributes' => [
                    'identification' => rand(9000000000000,9999999999999),
                    'name' => 'whenIneedMotivation...',
                    'properties' => 'MyOneSolutionIsMyQueen',
                    'deviceTypeId'  => 5
                ],
                "relationships" => [
                    "modifications" => [
                        "data" => [
                            [ "type" => "modifications", "id" => 1 ],
                            [ "type" => "modifications", "id" => 0 ],
                            [ "type" => "modifications", "id" => 3 ]
                        ]
                    ],
                    "carriers" => [
                        "data" => [
                            [ "type" => "carriers", "id" => 1 ],
                            [ "type" => "carriers", "id" => 2 ]
                        ]
                    ],
                    "companies" => [
                        "data" => [
                            [ "type" => "companies", "id" => 1 ],
                            [ "type" => "companies", "id" => 2 ]
                        ]
                    ],
                    "prices" => [
                        "data" => [
                            [ "type" => "prices", "capacityId" => 1, "styleId" => 2, "carrierId" => 1, "companyId" => 1, "priceRetail" => 100, "price1" => 100, "price2" => 100, "priceOwn" => 100 ],
                            [ "type" => "prices", "capacityId" => 1, "styleId" => 2, "carrierId" => 1, "companyId" => 2, "priceRetail" => 200, "price1" => 200, "price2" => 200, "priceOwn" => 200 ],
                            [ "type" => "prices", "capacityId" => 1, "styleId" => 2, "carrierId" => 2, "companyId" => 1, "priceRetail" => 300, "price1" => 300, "price2" => 300, "priceOwn" => 300 ],
                            [ "type" => "prices", "capacityId" => 1, "styleId" => 2, "carrierId" => 2, "companyId" => 2, "priceRetail" => 400, "price1" => 400, "price2" => 400, "priceOwn" => 400 ],
                            [ "type" => "prices", "capacityId" => 3, "styleId" => 2, "carrierId" => 1, "companyId" => 1, "priceRetail" => 500, "price1" => 500, "price2" => 500, "priceOwn" => 500 ],
                            [ "type" => "prices", "capacityId" => 3, "styleId" => 2, "carrierId" => 1, "companyId" => 2, "priceRetail" => 600, "price1" => 600, "price2" => 600, "priceOwn" => 600 ],
                            [ "type" => "prices", "capacityId" => 3, "styleId" => 2, "carrierId" => 2, "companyId" => 1, "priceRetail" => 700, "price1" => 700, "price2" => 700, "priceOwn" => 700 ],
                            [ "type" => "prices", "capacityId" => 3, "styleId" => 2, "carrierId" => 2, "companyId" => 2, "priceRetail" => 800, "price1" => 800, "price2" => 800, "priceOwn" => 800 ]
                        ]
                    ]
                ]
            ]
        ]
        )->seeJson(
        [
            'errors' => [
                'modifications' => 'the Device Modifications can not be created',
                'prices' => 'the Device Prices can not be created because other relationships can\'t be created'
            ]
        ]);
    }

    public function testCreateDeviceReturnPriceCarriersForeignKeyError() {

        $device = $this->post('/devices',
        [
            'data' => [
                'type' => 'devices',
                'attributes' => [
                    'identification' => rand(9000000000000,9999999999999),
                    'name' => 'whenIneedMotivation...',
                    'properties' => 'MyOneSolutionIsMyQueen',
                    'deviceTypeId'  => 5
                ],
                "relationships" => [
                    "modifications" => [
                        "data" => [
                            [ "type" => "modifications", "id" => 1 ],
                            [ "type" => "modifications", "id" => 2 ],
                            [ "type" => "modifications", "id" => 3 ]
                        ]
                    ],
                    "carriers" => [
                        "data" => [
                            [ "type" => "carriers", "id" => 0 ],
                            [ "type" => "carriers", "id" => 2 ]
                        ]
                    ],
                    "companies" => [
                        "data" => [
                            [ "type" => "companies", "id" => 1 ],
                            [ "type" => "companies", "id" => 2 ]
                        ]
                    ],
                    "prices" => [
                        "data" => [
                            [ "type" => "prices", "capacityId" => 1, "styleId" => 2, "carrierId" => 1, "companyId" => 1, "priceRetail" => 100, "price1" => 100, "price2" => 100, "priceOwn" => 100 ],
                            [ "type" => "prices", "capacityId" => 1, "styleId" => 2, "carrierId" => 1, "companyId" => 2, "priceRetail" => 200, "price1" => 200, "price2" => 200, "priceOwn" => 200 ],
                            [ "type" => "prices", "capacityId" => 1, "styleId" => 2, "carrierId" => 2, "companyId" => 1, "priceRetail" => 300, "price1" => 300, "price2" => 300, "priceOwn" => 300 ],
                            [ "type" => "prices", "capacityId" => 1, "styleId" => 2, "carrierId" => 2, "companyId" => 2, "priceRetail" => 400, "price1" => 400, "price2" => 400, "priceOwn" => 400 ],
                            [ "type" => "prices", "capacityId" => 3, "styleId" => 2, "carrierId" => 1, "companyId" => 1, "priceRetail" => 500, "price1" => 500, "price2" => 500, "priceOwn" => 500 ],
                            [ "type" => "prices", "capacityId" => 3, "styleId" => 2, "carrierId" => 1, "companyId" => 2, "priceRetail" => 600, "price1" => 600, "price2" => 600, "priceOwn" => 600 ],
                            [ "type" => "prices", "capacityId" => 3, "styleId" => 2, "carrierId" => 2, "companyId" => 1, "priceRetail" => 700, "price1" => 700, "price2" => 700, "priceOwn" => 700 ],
                            [ "type" => "prices", "capacityId" => 3, "styleId" => 2, "carrierId" => 2, "companyId" => 2, "priceRetail" => 800, "price1" => 800, "price2" => 800, "priceOwn" => 800 ]
                        ]
                    ]
                ]
            ]
        ]
        )->seeJson(
        [
            'errors' => [
                'carriers' => 'the Device Carriers can not be created',
                'prices' => 'the Device Prices can not be created because other relationships can\'t be created'
            ]
        ]);
    }

    public function testCreateDeviceReturnPriceCompaniesForeignKeyError() {

        $device = $this->post('/devices',
        [
            'data' => [
                'type' => 'devices',
                'attributes' => [
                    'identification' => rand(9000000000000,9999999999999),
                    'name' => 'whenIneedMotivation...',
                    'properties' => 'MyOneSolutionIsMyQueen',
                    'deviceTypeId'  => 5
                ],
                "relationships" => [
                    "modifications" => [
                        "data" => [
                            [ "type" => "modifications", "id" => 1 ],
                            [ "type" => "modifications", "id" => 2 ],
                            [ "type" => "modifications", "id" => 3 ]
                        ]
                    ],
                    "carriers" => [
                        "data" => [
                            [ "type" => "carriers", "id" => 1 ],
                            [ "type" => "carriers", "id" => 2 ]
                        ]
                    ],
                    "companies" => [
                        "data" => [
                            [ "type" => "companies", "id" => 0 ],
                            [ "type" => "companies", "id" => 2 ]
                        ]
                    ],
                    "prices" => [
                        "data" => [
                            [ "type" => "prices", "capacityId" => 1, "styleId" => 2, "carrierId" => 1, "companyId" => 1, "priceRetail" => 100, "price1" => 100, "price2" => 100, "priceOwn" => 100 ],
                            [ "type" => "prices", "capacityId" => 1, "styleId" => 2, "carrierId" => 1, "companyId" => 2, "priceRetail" => 200, "price1" => 200, "price2" => 200, "priceOwn" => 200 ],
                            [ "type" => "prices", "capacityId" => 1, "styleId" => 2, "carrierId" => 2, "companyId" => 1, "priceRetail" => 300, "price1" => 300, "price2" => 300, "priceOwn" => 300 ],
                            [ "type" => "prices", "capacityId" => 1, "styleId" => 2, "carrierId" => 2, "companyId" => 2, "priceRetail" => 400, "price1" => 400, "price2" => 400, "priceOwn" => 400 ],
                            [ "type" => "prices", "capacityId" => 3, "styleId" => 2, "carrierId" => 1, "companyId" => 1, "priceRetail" => 500, "price1" => 500, "price2" => 500, "priceOwn" => 500 ],
                            [ "type" => "prices", "capacityId" => 3, "styleId" => 2, "carrierId" => 1, "companyId" => 2, "priceRetail" => 600, "price1" => 600, "price2" => 600, "priceOwn" => 600 ],
                            [ "type" => "prices", "capacityId" => 3, "styleId" => 2, "carrierId" => 2, "companyId" => 1, "priceRetail" => 700, "price1" => 700, "price2" => 700, "priceOwn" => 700 ],
                            [ "type" => "prices", "capacityId" => 3, "styleId" => 2, "carrierId" => 2, "companyId" => 2, "priceRetail" => 800, "price1" => 800, "price2" => 800, "priceOwn" => 800 ]
                        ]
                    ]
                ]
            ]
        ]
        )->seeJson(
        [
            'errors' => [
                'companies' => 'the Device Companies can not be created',
                'prices' => 'the Device Prices can not be created because other relationships can\'t be created'
            ]
        ]);
    }

    public function testCreateDeviceReturnPriceCheckIfPriceRowIsCorrect() {

        $device = $this->post('/devices',
        [
            'data' => [
                'type' => 'devices',
                'attributes' => [
                    'identification' => rand(9000000000000,9999999999999),
                    'name' => 'whenIneedMotivation...',
                    'properties' => 'MyOneSolutionIsMyQueen',
                    'deviceTypeId'  => 5
                ],
                "relationships" => [
                    "modifications" => [
                        "data" => [
                            [ "type" => "modifications", "id" => 1 ],
                            [ "type" => "modifications", "id" => 2 ],
                            [ "type" => "modifications", "id" => 3 ]
                        ]
                    ],
                    "carriers" => [
                        "data" => [
                            [ "type" => "carriers", "id" => 1 ],
                            [ "type" => "carriers", "id" => 2 ]
                        ]
                    ],
                    "companies" => [
                        "data" => [
                            [ "type" => "companies", "id" => 1 ],
                            [ "type" => "companies", "id" => 2 ]
                        ]
                    ],
                    "prices" => [
                        "data" => [
                            [ "type" => "prices", "capacityId" => 1000, "styleId" => 2, "carrierId" => 1, "companyId" => 1, "priceRetail" => 100, "price1" => 100, "price2" => 100, "priceOwn" => 100 ],
                            [ "type" => "prices", "capacityId" => 1, "styleId" => 2, "carrierId" => 1, "companyId" => 2, "priceRetail" => 200, "price1" => 200, "price2" => 200, "priceOwn" => 200 ],
                            [ "type" => "prices", "capacityId" => 1, "styleId" => 2, "carrierId" => 2, "companyId" => 1, "priceRetail" => 300, "price1" => 300, "price2" => 300, "priceOwn" => 300 ],
                            [ "type" => "prices", "capacityId" => 1, "styleId" => 2, "carrierId" => 2, "companyId" => 2, "priceRetail" => 400, "price1" => 400, "price2" => 400, "priceOwn" => 400 ],
                            [ "type" => "prices", "capacityId" => 3, "styleId" => 2, "carrierId" => 1, "companyId" => 1, "priceRetail" => 500, "price1" => 500, "price2" => 500, "priceOwn" => 500 ],
                            [ "type" => "prices", "capacityId" => 3, "styleId" => 2, "carrierId" => 1, "companyId" => 2, "priceRetail" => 600, "price1" => 600, "price2" => 600, "priceOwn" => 600 ],
                            [ "type" => "prices", "capacityId" => 3, "styleId" => 2, "carrierId" => 2, "companyId" => 1, "priceRetail" => 700, "price1" => 700, "price2" => 700, "priceOwn" => 700 ],
                            [ "type" => "prices", "capacityId" => 3, "styleId" => 2, "carrierId" => 2, "companyId" => 2, "priceRetail" => 800, "price1" => 800, "price2" => 800, "priceOwn" => 800 ]
                        ]
                    ]
                ]
            ]
        ]
        )->seeJson(
        [
            'errors' => [
                'prices' => 'the Device Prices can not be created'
            ]
        ]);
    }

    public function testUpdateDevice() {
        $device = factory(\WA\DataStore\Device\Device::class)->create();

        $this->put('/devices/'.$device->id, 
            [
                'data' => [
                    'type' => 'devices',
                    'attributes' => [
                        'identification' => rand(9000000000000,9999999999999),
                        'name' => 'whenIneedMotivation...',
                        'properties' => 'MyOneSolutionIsMyQueen',
                        'deviceTypeId'  => 5
                    ],
                    'relationships' => [
                        'assets' => [
                            'data' => [
                                [ 'type' => 'assets', 'id' => '1' ],
                                [ 'type' => 'assets', 'id' => '2' ]
                            ]
                        ],
                        'modifications' => [
                            'data' => [
                                [ 'type' => 'modifications', 'id' => '1' ],
                                [ 'type' => 'modifications', 'id' => '2' ],
                                [ 'type' => 'modifications', 'id' => '3' ]
                            ]
                        ],
                        'carriers' => [
                            'data' => [
                                [ 'type' => 'carriers', 'id' => '1' ],
                                [ 'type' => 'carriers', 'id' => '2' ]
                            ]
                        ],
                        'companies' => [
                            'data' => [
                                [ 'type' => 'companies', 'id' => '1' ],
                                [ 'type' => 'companies', 'id' => '2' ]
                            ]
                        ],
                        'prices' => [
                            'data' => [
                                [ 'type' => 'prices', 'capacityId' => 1, 'styleId' => 2, 'carrierId' => 1, 'companyId' => 1, 'priceRetail' => 100, 'price1' => 100, 'price2' => 100, 'priceOwn' => 100 ],
                                [ 'type' => 'prices', 'capacityId' => 1, 'styleId' => 2, 'carrierId' => 1, 'companyId' => 2, 'priceRetail' => 200, 'price1' => 200, 'price2' => 200, 'priceOwn' => 200 ],
                                [ 'type' => 'prices', 'capacityId' => 1, 'styleId' => 2, 'carrierId' => 2, 'companyId' => 1, 'priceRetail' => 300, 'price1' => 300, 'price2' => 300, 'priceOwn' => 300 ],
                                [ 'type' => 'prices', 'capacityId' => 1, 'styleId' => 2, 'carrierId' => 2, 'companyId' => 2, 'priceRetail' => 400, 'price1' => 400, 'price2' => 400, 'priceOwn' => 400 ],
                                [ 'type' => 'prices', 'capacityId' => 3, 'styleId' => 2, 'carrierId' => 1, 'companyId' => 1, 'priceRetail' => 500, 'price1' => 500, 'price2' => 500, 'priceOwn' => 500 ],
                                [ 'type' => 'prices', 'capacityId' => 3, 'styleId' => 2, 'carrierId' => 1, 'companyId' => 2, 'priceRetail' => 600, 'price1' => 600, 'price2' => 600, 'priceOwn' => 600 ],
                                [ 'type' => 'prices', 'capacityId' => 3, 'styleId' => 2, 'carrierId' => 2, 'companyId' => 1, 'priceRetail' => 700, 'price1' => 700, 'price2' => 700, 'priceOwn' => 700 ],
                                [ 'type' => 'prices', 'capacityId' => 3, 'styleId' => 2, 'carrierId' => 2, 'companyId' => 2, 'priceRetail' => 800, 'price1' => 800, 'price2' => 800, 'priceOwn' => 800 ]
                            ]
                        ]
                    ]
                ]
            ])
            ->seeJson(
            [
                'type' => 'devices',
                'name' => 'whenIneedMotivation...',
                'properties' => 'MyOneSolutionIsMyQueen',
                'deviceTypeId'  => 5
            ]);
    }

    public function testDeleteDeviceIfExists() {
        // CREATE & DELETE
        $device = factory(\WA\DataStore\Device\Device::class)->create();
        $responseDel1 = $this->call('DELETE', '/devices/'.$device->id);
        $this->assertEquals(200, $responseDel1->status());
        $responseGet1 = $this->call('GET', '/devices/'.$device->id);
        $this->assertEquals(409, $responseGet1->status());        
    }

    public function testDeleteDeviceIfNoExists(){
        // DELETE NO EXISTING.
        $responseDel2 = $this->call('DELETE', '/devices/1000000');
        $this->assertEquals(409, $responseDel2->status());
    }



    /*
     *      Transforms an Exception Object and gets the value of the Error Message.
     *
     *      @param: 
     *          \Exception $e
     *      @return:
     *          $error->getValue($e);
     */
    private function getProtectedId($info){
        try{
            $reflectorResponse = new \ReflectionClass($info);
            $classResponse = $reflectorResponse->getProperty('response');
            $classResponse->setAccessible(true);
            $dataResponse = $classResponse->getValue($info);
            return json_decode($dataResponse->getContent())->data->id;    
        } catch (\Exception $e){
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
*/

/* EXAMPLE POST DEVICE
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

*/

/*
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
