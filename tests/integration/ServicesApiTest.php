<?php

use Laravel\Lumen\Testing\DatabaseTransactions;

class ServicesApiTest extends TestCase
{
    use DatabaseTransactions;     
     

    /**
     * A basic functional test for services
     *
     *
     */
    public function testGetServices()
    {       
        $res = $this->json('GET', 'services');

        $res->seeJsonStructure([
                'data' => [
                    0 => [ 'type','id',
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
                            'created_at',
                            'updated_at',
                        ],
                        'links'
                    ]
                ]
            ]);
    }

    public function testGetServiceById()
    {
        $service = factory(\WA\DataStore\Service\Service::class)->create();

        $res = $this->json('GET', 'services/'.$service->id)
            ->seeJson([
                'type' => 'services',
                'title'=> $service->title,
                'planCode'=> $service->planCode,
                'cost'=> $service->cost,
                'description'=> $service->description,
                'domesticMinutes'=> $service->domesticMinutes,
                'domesticData'=> $service->domesticData,
                'domesticMessages'=> $service->domesticMessages,
                'internationalMinutes'=> $service->internationalMinutes,
                'internationalData'=> $service->internationalData,
                'internationalMessages'=> $service->internationalMessages,
            ]);
    }

    public function testCreateService()
    {
        $this->post('/services',
            [
                'title' => 'Service Test',
                'planCode' => 11111,
                'cost' => 22,
                'description' => 'Test Service',
                'domesticMinutes' => 111,
                'domesticData' => 222,
                'domesticMessages' => 333,
                'internationalMinutes' => 444,
                'internationalData' => 555,
                'internationalMessages' => 666,
            ])
            ->seeJson([
                'type' => 'services',
                'title' => 'Service Test',
                'planCode' => 11111,
                'cost' => 22,
                'description' => 'Test Service',
                'domesticMinutes' => 111,
                'domesticData' => 222,
                'domesticMessages' => 333,
                'internationalMinutes' => 444,
                'internationalData' => 555,
                'internationalMessages' => 666,
            ]);
    }

    public function testUpdateService()
    {
        $service = factory(\WA\DataStore\Service\Service::class)->create();

        $this->put('/services/'.$service->id, [
                'title'=> 'Test Update',
                'planCode'=> $service->planCode,
                'cost'=> $service->cost,
                'description'=> $service->description,
                'domesticMinutes'=> $service->domesticMinutes,
                'domesticData'=> $service->domesticData,
                'domesticMessages'=> $service->domesticMessages,
                'internationalMinutes'=> $service->internationalMinutes,
                'internationalData'=> $service->internationalData,
                'internationalMessages'=> $service->internationalMessages,
            ])
            ->seeJson([
                'type' => 'services',
                'title'=> 'Test Update',
                'planCode'=> $service->planCode,
                'cost'=> $service->cost,
                'description'=> $service->description,
                'domesticMinutes'=> $service->domesticMinutes,
                'domesticData'=> $service->domesticData,
                'domesticMessages'=> $service->domesticMessages,
                'internationalMinutes'=> $service->internationalMinutes,
                'internationalData'=> $service->internationalData,
                'internationalMessages'=> $service->internationalMessages,
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