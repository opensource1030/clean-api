<?php

//use Laravel\Lumen\Testing\DatabaseTransactions;
use Laravel\Lumen\Testing\DatabaseMigrations;

class ServicesApiTest extends TestCase
{
    //use DatabaseTransactions;
    use DatabaseMigrations;

    /**
     * A basic functional test for services.
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

    public function testGetServiceById()
    {
        $service = factory(\WA\DataStore\Service\Service::class)->create();

        $res = $this->get('services/'.$service->id)
            ->seeJson([
                'type' => 'services',
                'title' => $service->title,
                'planCode' => "$service->planCode",
                'cost' => "$service->cost",
                'description' => $service->description,
                'domesticMinutes' => "$service->domesticMinutes",
                'domesticData' => "$service->domesticData",
                'domesticMessages' => "$service->domesticMessages",
                'internationalMinutes' => "$service->internationalMinutes",
                'internationalData' => "$service->internationalData",
                'internationalMessages' => "$service->internationalMessages",
            ]);
    }

    public function testCreateService()
    {
        $this->post('/services',
            [
                'data' => [
                    'type' => 'services',
                    'attributes' => [
                        'title' => 'Service Test',
                        'planCode' => '11111',
                        'cost' => '22',
                        'description' => 'Test Service',
                        'domesticMinutes' => '111',
                        'domesticData' => '222',
                        'domesticMessages' => '333',
                        'internationalMinutes' => '444',
                        'internationalData' => '555',
                        'internationalMessages' => '666',
                    ],
                ],
            ])
            ->seeJson([
                'type' => 'services',
                'title' => 'Service Test',
                'planCode' => '11111',
                'cost' => '22',
                'description' => 'Test Service',
                'domesticMinutes' => '111',
                'domesticData' => '222',
                'domesticMessages' => '333',
                'internationalMinutes' => '444',
                'internationalData' => '555',
                'internationalMessages' => '666',
            ]);
    }

    public function testUpdateService()
    {
        $service = factory(\WA\DataStore\Service\Service::class)->create(
            ['title' => 'title1', 'planCode' => 11111, 'cost' => 30, 'description' => 'desc1', 'domesticMinutes' => 100, 'domesticData' => 100, 'domesticMessages' => 100, 'internationalMinutes' => 100, 'internationalData' => 100, 'internationalMessages' => 100]
        );
        $serviceAux = factory(\WA\DataStore\Service\Service::class)->create(
            ['title' => 'title2', 'planCode' => 22222, 'cost' => 40, 'description' => 'desc2', 'domesticMinutes' => 200, 'domesticData' => 200, 'domesticMessages' => 200, 'internationalMinutes' => 200, 'internationalData' => 200, 'internationalMessages' => 200]
        );

        $this->assertNotEquals($service->id, $serviceAux->id);
        $this->assertNotEquals($service->title, $serviceAux->title);
        $this->assertNotEquals($service->cost, $serviceAux->cost);
        $this->assertNotEquals($service->description, $serviceAux->description);
        $this->assertNotEquals($service->domesticMinutes, $serviceAux->domesticMinutes);
        $this->assertNotEquals($service->domesticData, $serviceAux->domesticData);
        $this->assertNotEquals($service->domesticMessages, $serviceAux->domesticMessages);
        $this->assertNotEquals($service->internationalMinutes, $serviceAux->internationalMinutes);
        $this->assertNotEquals($service->internationalData, $serviceAux->internationalData);
        $this->assertNotEquals($service->internationalMessages, $serviceAux->internationalMessages);

        $this->put('/services/'.$serviceAux->id,
            [
                'data' => [
                    'type' => 'services',
                    'attributes' => [
                        'title' => "$service->title",
                        'planCode' => "$service->planCode",
                        'cost' => "$service->cost",
                        'description' => $service->description,
                        'domesticMinutes' => "$service->domesticMinutes",
                        'domesticData' => "$service->domesticData",
                        'domesticMessages' => "$service->domesticMessages",
                        'internationalMinutes' => "$service->internationalMinutes",
                        'internationalData' => "$service->internationalData",
                        'internationalMessages' => "$service->internationalMessages",
                    ],
                ],
            ])
            ->seeJson([
                'type' => 'services',
                'title' => 'title1',
                'planCode' => '11111',
                'cost' => '30',
                'description' => 'desc1',
                'domesticMinutes' => '100',
                'domesticData' => '100',
                'domesticMessages' => '100',
                'internationalMinutes' => '100',
                'internationalData' => '100',
                'internationalMessages' => '100',
            ]);
    }

    public function testDeleteServiceIfExists()
    {
        // CREATE & DELETE
        $service = factory(\WA\DataStore\Service\Service::class)->create();
        $responseDel = $this->call('DELETE', '/services/'.$service->id);
        $this->assertEquals(200, $responseDel->status());
        $responseGet = $this->call('GET', '/services/'.$service->id);
        $this->assertEquals(409, $responseGet->status());
    }

    public function testDeleteServiceIfNoExists()
    {
        // DELETE NO EXISTING.
        $responseDel = $this->call('DELETE', '/services/1');
        $this->assertEquals(404, $responseDel->status());
    }
}
