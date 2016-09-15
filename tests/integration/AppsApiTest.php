<?php

//use Laravel\Lumen\Testing\DatabaseTransactions;
use Laravel\Lumen\Testing\DatabaseMigrations;

use WA\DataStore\Apps\Apps;

class AppsApiTest extends TestCase
{
    //use DatabaseTransactions;
    use DatabaseMigrations;

    /**
     * A basic functional test for apps
     *
     *
     */
    public function testGetApps() {       
        
        factory(\WA\DataStore\App\App::class, 40)->create();

        $res = $this->json('GET', 'apps');

        $res->seeJsonStructure([
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
                'self',
                'first',
                'next',
                'last'
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

        $this->post('/apps',
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

        $this->put('/apps/'.$app->id, [
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

    public function testDeleteApp() {
        
        $app = factory(\WA\DataStore\App\App::class)->create();
        $this->delete('/apps/'. $app->id);
        $response = $this->call('GET', '/apps/'.$app->id);
        $this->assertEquals(500, $response->status());
    }
}