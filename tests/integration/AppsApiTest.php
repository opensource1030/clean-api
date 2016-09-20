<?php

use Laravel\Lumen\Testing\DatabaseMigrations;

use WA\DataStore\Apps\Apps;

class AppsApiTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * A basic functional test for Apps
     */
    public function testGetApps() {       
        
        factory(\WA\DataStore\App\App::class, 40)->create();

        $this->json('GET', 'apps')
            ->seeJsonStructure([
            'data' => [
                0 => [  
                    'type',
                    'id',
                    'attributes' => [
                        'type',
                        'image',
                        'description',
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
                'self'
            ]
        ]);
    }

    public function testGetAppById() {

        $app = factory(\WA\DataStore\App\App::class)->create();

        $res = $this->json('GET', 'apps/'.$app->id)
            ->seeJson([
                'type' => 'apps',
                'type' => $app->type,
                'image'=> $app->image,
                'description'=> $app->description,
            ]);
    }

    public function testCreateApp() {

        $this->json('POST', 'apps',
            [
                'type' => 'AppType',
                'image'=> 'AppImageLink',
                'description'=> 'AppDescription',
            ])
            ->seeJson([
                'type' => 'apps',
                'type' => 'AppType',
                'image'=> 'AppImageLink',
                'description'=> 'AppDescription',
            ]);
    }

    public function testUpdateApp() {

        $app = factory(\WA\DataStore\App\App::class)->create();

        $this->json('PUT', 'apps/'.$app->id, [
                'type' => 'AppTypeEdit',
                'image'=> 'AppImageLinkEdit',
                'description'=> 'AppDescriptionEdit',
            ])
            ->seeJson([
                'type' => 'apps',
                'type' => 'AppTypeEdit',
                'image'=> 'AppImageLinkEdit',
                'description'=> 'AppDescriptionEdit',
            ]);
    }

    public function testDeleteAppIfExists() {

        // CREATE & DELETE
        $app = factory(\WA\DataStore\App\App::class)->create();
        $responseDel = $this->call('DELETE', 'apps/'.$app->id);
        $this->assertEquals(200, $responseDel->status());
        $responseGet = $this->call('GET', 'apps/'.$app->id);
        $this->assertEquals(409, $responseGet->status());        
    }

    public function testDeleteAppIfNoExists(){

        // DELETE NO EXISTING.
        $responseDel = $this->call('DELETE', 'apps/1');
        $this->assertEquals(409, $responseDel->status());
    }
}