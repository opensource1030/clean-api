<?php

//use Laravel\Lumen\Testing\DatabaseTransactions;
use Laravel\Lumen\Testing\DatabaseMigrations;

class ServicesApiTest extends TestCase
{
    //use DatabaseTransactions;
    use DatabaseMigrations;     

    /**
     * A basic functional test for services
     *
     *
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

    public function testGetServiceById()
    {
        $service = factory(\WA\DataStore\Service\Service::class)->create();

        $res = $this->get('services/'.$service->id)
            ->seeJson([
                'type' => 'services',
                'title'=> $service->title,
                'planCode'=> "$service->planCode",
                'cost'=> "$service->cost",
                'description'=> $service->description,
                'domesticMinutes'=> "$service->domesticMinutes",
                'domesticData'=> "$service->domesticData",
                'domesticMessages'=> "$service->domesticMessages",
                'internationalMinutes'=> "$service->internationalMinutes",
                'internationalData'=> "$service->internationalData",
                'internationalMessages'=> "$service->internationalMessages",
            ]);
    }

    public function testCreateService()
    {
        $this->post('/services',
            [
                'title' => 'Service Test',
                'planCode' => "11111",
                'cost' => "22",
                'description' => 'Test Service',
                'domesticMinutes' => "111",
                'domesticData' => "222",
                'domesticMessages' => "333",
                'internationalMinutes' => "444",
                'internationalData' => "555",
                'internationalMessages' => "666",
            ])
            ->seeJson([
                'type' => 'services',
                'title' => 'Service Test',
                'planCode' => "11111",
                'cost' => "22",
                'description' => 'Test Service',
                'domesticMinutes' => "111",
                'domesticData' => "222",
                'domesticMessages' => "333",
                'internationalMinutes' => "444",
                'internationalData' => "555",
                'internationalMessages' => "666",
            ]);
    }

    public function testUpdateService()
    {
        $service = factory(\WA\DataStore\Service\Service::class)->create();

        $this->put('/services/'.$service->id, [
                'title'=> 'Test Update',
                'planCode'=> "$service->planCode",
                'cost'=> "$service->cost",
                'description'=> $service->description,
                'domesticMinutes'=> "$service->domesticMinutes",
                'domesticData'=> "$service->domesticData",
                'domesticMessages'=> "$service->domesticMessages",
                'internationalMinutes'=> "$service->internationalMinutes",
                'internationalData'=> "$service->internationalData",
                'internationalMessages'=> "$service->internationalMessages",
            ])
            ->seeJson([
                'type' => 'services',
                'title'=> 'Test Update',
                'planCode'=> "$service->planCode",
                'cost'=> "$service->cost",
                'description'=> $service->description,
                'domesticMinutes'=> "$service->domesticMinutes",
                'domesticData'=> "$service->domesticData",
                'domesticMessages'=> "$service->domesticMessages",
                'internationalMinutes'=> "$service->internationalMinutes",
                'internationalData'=> "$service->internationalData",
                'internationalMessages'=> "$service->internationalMessages",
            ]);
    }

    public function testDeleteService()
    {
        $service = factory(\WA\DataStore\Service\Service::class)->create();
        $this->delete('/services/'. $service->id);
        $response = $this->call('GET', '/services/'.$service->id);
        $this->assertEquals(500, $response->status());
    }

}