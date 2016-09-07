<?php

use Laravel\Lumen\Testing\DatabaseTransactions;

class DevicesApiTest extends TestCase
{

    use DatabaseTransactions;


    /**
     * A basic functional test for devices
     *
     *
     */
    public function testGetDevices()
    {
        $res = $this->json('GET', 'devices');

        $res->seeJsonStructure([
            'data' => [
                0 => [  
                    'type',
                    'id',
                    'attributes' => [
                        'identification',
                        'image',
                        'name',
                        'properties',
                        'externalId',
                        'deviceTypeId',
                        'statusId',
                        'syncId',
                        'created_at',
                        'updated_at'
                    ],
                    'links'
                ]
            ]
        ]);
    }

    public function testGetDeviceById()
    {
        // CREATE & GET
        $device = factory(\WA\DataStore\Device\Device::class)->create();

        $res = $this->json('GET', 'devices/'.$device->id)
            ->seeJson([
                'type' => 'devices',
                'identification'=> $device->identification,
                'image'=> $device->image,
                'name'=> $device->name,
                'properties'=> $device->properties,
                'externalId'=> $device->externalId,
                'deviceTypeId'=> $device->deviceTypeId,
                'statusId'=> $device->statusId,
                'syncId'=> $device->syncId
            ]);

        // GET NO CREATED
        $response = $this->call('GET', '/devices/1000000');
        $this->assertEquals(409, $response->status());
    }

    public function testCreateDevice()
    {
        $device = $this->post('/devices',
            [
                'data' => [
                    'type' => 'devices',
                    'attributes' => [
                        'identification' => rand(9000000000000,9999999999999),
                        'image' => 'http://static1.uk.businessinsider.com/image/56a24731c08a80431d8b90d5-960/iphone-7-concept-curved-display.jpg',
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
                                [ 'type' => 'prices', 'capacityId' => 1, 'styleId' => 3, 'carrierId' => 1, 'companyId' => 1, 'priceRetail' => 100, 'price1' => 100, 'price2' => 100, 'priceOwn' => 100 ],
                                [ 'type' => 'prices', 'capacityId' => 2, 'styleId' => 3, 'carrierId' => 1, 'companyId' => 1, 'priceRetail' => 200, 'price1' => 200, 'price2' => 200, 'priceOwn' => 200 ],
                                [ 'type' => 'prices', 'capacityId' => 1, 'styleId' => 3, 'carrierId' => 1, 'companyId' => 2, 'priceRetail' => 300, 'price1' => 300, 'price2' => 300, 'priceOwn' => 300 ],
                                [ 'type' => 'prices', 'capacityId' => 2, 'styleId' => 3, 'carrierId' => 1, 'companyId' => 2, 'priceRetail' => 400, 'price1' => 400, 'price2' => 400, 'priceOwn' => 400 ],
                                [ 'type' => 'prices', 'capacityId' => 1, 'styleId' => 3, 'carrierId' => 2, 'companyId' => 1, 'priceRetail' => 500, 'price1' => 500, 'price2' => 500, 'priceOwn' => 500 ],
                                [ 'type' => 'prices', 'capacityId' => 2, 'styleId' => 3, 'carrierId' => 2, 'companyId' => 1, 'priceRetail' => 600, 'price1' => 600, 'price2' => 600, 'priceOwn' => 600 ],
                                [ 'type' => 'prices', 'capacityId' => 1, 'styleId' => 3, 'carrierId' => 2, 'companyId' => 2, 'priceRetail' => 700, 'price1' => 700, 'price2' => 700, 'priceOwn' => 700 ],
                                [ 'type' => 'prices', 'capacityId' => 2, 'styleId' => 3, 'carrierId' => 2, 'companyId' => 2, 'priceRetail' => 800, 'price1' => 800, 'price2' => 800, 'priceOwn' => 800 ]
                            ]
                        ]
                    ]
                ]
            ]
            )->seeJson(
            [
                'type' => 'devices',
                'image' => 'http://static1.uk.businessinsider.com/image/56a24731c08a80431d8b90d5-960/iphone-7-concept-curved-display.jpg',
                'name' => 'whenIneedMotivation...',
                'properties' => 'MyOneSolutionIsMyQueen',
                'deviceTypeId'  => 5
            ]);
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
    }


    public function testUpdateDevice()
    {
        $device = factory(\WA\DataStore\Device\Device::class)->create();

        $this->put('/devices/'.$device->id, 
            [
                'data' => [
                    'type' => 'devices',
                    'attributes' => [
                        'identification' => rand(9000000000000,9999999999999),
                        'image' => 'http://static1.uk.businessinsider.com/image/56a24731c08a80431d8b90d5-960/iphone-7-concept-curved-display.jpg',
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
                                [ 'type' => 'prices', 'capacityId' => 1, 'styleId' => 3, 'carrierId' => 1, 'companyId' => 1, 'priceRetail' => 100, 'price1' => 100, 'price2' => 100, 'priceOwn' => 100 ],
                                [ 'type' => 'prices', 'capacityId' => 2, 'styleId' => 3, 'carrierId' => 1, 'companyId' => 1, 'priceRetail' => 200, 'price1' => 200, 'price2' => 200, 'priceOwn' => 200 ],
                                [ 'type' => 'prices', 'capacityId' => 1, 'styleId' => 3, 'carrierId' => 1, 'companyId' => 2, 'priceRetail' => 300, 'price1' => 300, 'price2' => 300, 'priceOwn' => 300 ],
                                [ 'type' => 'prices', 'capacityId' => 2, 'styleId' => 3, 'carrierId' => 1, 'companyId' => 2, 'priceRetail' => 400, 'price1' => 400, 'price2' => 400, 'priceOwn' => 400 ],
                                [ 'type' => 'prices', 'capacityId' => 1, 'styleId' => 3, 'carrierId' => 2, 'companyId' => 1, 'priceRetail' => 500, 'price1' => 500, 'price2' => 500, 'priceOwn' => 500 ],
                                [ 'type' => 'prices', 'capacityId' => 2, 'styleId' => 3, 'carrierId' => 2, 'companyId' => 1, 'priceRetail' => 600, 'price1' => 600, 'price2' => 600, 'priceOwn' => 600 ],
                                [ 'type' => 'prices', 'capacityId' => 1, 'styleId' => 3, 'carrierId' => 2, 'companyId' => 2, 'priceRetail' => 700, 'price1' => 700, 'price2' => 700, 'priceOwn' => 700 ],
                                [ 'type' => 'prices', 'capacityId' => 2, 'styleId' => 3, 'carrierId' => 2, 'companyId' => 2, 'priceRetail' => 800, 'price1' => 800, 'price2' => 800, 'priceOwn' => 800 ]
                            ]
                        ]
                    ]
                ]
            ])
            ->seeJson(
            [
                'type' => 'devices',
                'image' => 'http://static1.uk.businessinsider.com/image/56a24731c08a80431d8b90d5-960/iphone-7-concept-curved-display.jpg',
                'name' => 'whenIneedMotivation...',
                'properties' => 'MyOneSolutionIsMyQueen',
                'deviceTypeId'  => 5
            ]);
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
    }

    public function testDeleteDevice()
    {
        // CREATE & DELETE
        $device = factory(\WA\DataStore\Device\Device::class)->create();
        $responseDel1 = $this->call('DELETE', '/devices/'.$device->id);
        $this->assertEquals(200, $responseDel1->status());
        $responseGet1 = $this->call('GET', '/devices/'.$device->id);
        $this->assertEquals(409, $responseGet1->status());
        
        // DELETE NO EXISTING.
        $responseDel2 = $this->call('DELETE', '/devices/1000000');
        $this->assertEquals(409, $responseDel2->status());
    }
}