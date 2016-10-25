<?php

use Laravel\Lumen\Testing\DatabaseMigrations;

use Laravel\Lumen\Testing\DatabaseTransactions;
use WA\Http\Controllers\RelationshipsController;

class RelationshipsTest extends TestCase
{
    use DatabaseMigrations;

	public function testIncludeRelationships()
    {
        $device = factory(\WA\DataStore\Device\Device::class)->create();

        $price1 = factory(\WA\DataStore\Price\Price::class)->create(['deviceId' => $device->id])->id;
        $price2 = factory(\WA\DataStore\Price\Price::class)->create(['deviceId' => $device->id])->id;

        $this->json('GET', 'devices/'.$device->id.'/relationships/prices')
            ->seeJsonStructure([
                'data' => [
                    0 => [
                        'type',
                        'id',
                        'attributes' => [],
                        'links' => [
                            'self'
                        ]
                    ]
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
                        'total_pages'
                    ]
                ],
                'links' => [
                    'self',
                    'first',
                    'last'
                ]
            ]);
    }

    public function testIncludeRelationshipsErrors()
    {
        $device = factory(\WA\DataStore\Device\Device::class)->create();

        $price1 = factory(\WA\DataStore\Price\Price::class)->create(['deviceId' => $device->id])->id;
        $price2 = factory(\WA\DataStore\Price\Price::class)->create(['deviceId' => $device->id])->id;

        $this->json('GET', 'notexists/'.$device->id.'/relationships/prices')
            ->seeJson([
                'errors' => [
                    'notexists' => 'the Notexist selected doesn\'t exists'
                ]
            ]);

        $this->json('GET', 'devices/'.$device->id.'/relationships/notexists')
            ->seeJson([
                'errors' => [
                    'devices' => 'the notexists selected doesn\'t exists'
                ]
            ]);

        $deviceId = factory(\WA\DataStore\Device\Device::class)->create()->id;

        $res = $this->json('GET', 'devices/'.$deviceId.'/relationships/prices')
            ->seeJsonStructure([
                'total',
                'per_page',
                'current_page',
                'last_page',
                'next_page_url',
                'prev_page_url',
                'from',
                'to',
                'data'
            ]);

        $idNotExists = $deviceId + 10;

        $this->json('GET', 'devices/'.$idNotExists.'/relationships/prices')
            ->seeJson([
                'errors' => [
                    'devices' => 'the Device selected doesn\'t exists'
                ]
            ]);
    }

    public function testIncludeRelationshipsInformation()
    {
        $device = factory(\WA\DataStore\Device\Device::class)->create();

        $price1 = factory(\WA\DataStore\Price\Price::class)->create(['deviceId' => $device->id])->id;
        $price2 = factory(\WA\DataStore\Price\Price::class)->create(['deviceId' => $device->id])->id;

        $this->json('GET', 'devices/'.$device->id.'/prices')
            ->seeJsonStructure([
                'data' => [
                    0 => [
                        'type',
                        'id',
                        'attributes' => [
                            'priceRetail',
                            'price1',
                            'price2',
                            'priceOwn',
                            'deviceId',
                            'capacityId',
                            'styleId',
                            'carrierId',
                            'companyId',
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
                    'sort',
                    'filter',
                    'fields',
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
                    'last'
                ]
            ]);
    }

    public function testIncludeRelationshipsInformationErrors()
    {
        $device = factory(\WA\DataStore\Device\Device::class)->create();

        $price1 = factory(\WA\DataStore\Price\Price::class)->create(['deviceId' => $device->id])->id;
        $price2 = factory(\WA\DataStore\Price\Price::class)->create(['deviceId' => $device->id])->id;

        $this->json('GET', 'notexists/'.$device->id.'/prices')
            ->seeJson([
                'errors' => [
                    'notexists' => 'the Notexist selected doesn\'t exists'
                ]
            ]);

        $this->json('GET', 'devices/'.$device->id.'/notexists')
            ->seeJson([
                'errors' => [
                    'devices' => 'the notexists selected doesn\'t exists'
                ]
            ]);

        $deviceId = factory(\WA\DataStore\Device\Device::class)->create()->id;

        $res = $this->json('GET', 'devices/'.$deviceId.'/relationships/prices')
            ->seeJsonStructure([
                'total',
                'per_page',
                'current_page',
                'last_page',
                'next_page_url',
                'prev_page_url',
                'from',
                'to',
                'data'
            ]);

        $idNotExists = $deviceId + 10;

        $this->json('GET', 'devices/'.$idNotExists.'/relationships/prices')
            ->seeJson([
                'errors' => [
                    'devices' => 'the Device selected doesn\'t exists'
                ]
            ]);
    }

}