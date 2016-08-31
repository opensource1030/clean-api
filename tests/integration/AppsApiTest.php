<?php

use Laravel\Lumen\Testing\DatabaseTransactions;

class AppsApiTest extends TestCase
{
    use DatabaseTransactions;     
     

    /**
     * A basic functional test for services
     *
     *
     */
    public function testGetApps()
    {       
        $res = $this->json('GET', 'apps');

        $res->seeJsonStructure([
                'data' => [
                    0 => [ 'type','id',
                        'attributes' => [
                            'type',
                            'image',
                            'description',
                            'created_at',
                            'updated_at',
                        ],
                        'links'
                    ]
                ]
            ]);
    }

    public function testGetAppById()
    {
        $app = factory(\WA\DataStore\App\App::class)->create();

        $res = $this->json('GET', 'apps/'.$app->id)
            ->seeJson([
                'type' => 'apps',
                'type' => $app->type,
                'image'=> $app->image,
                'description'=> $app->description,
            ]);
    }

    public function testCreateApp()
    {
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

    public function testUpdateApp()
    {
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

    public function testDeleteApp()
    {
        $app = factory(\WA\DataStore\App\App::class)->create();
        $this->delete('/apps/'. $app->id);
        $response = $this->call('GET', '/apps/'.$app->id);
        $this->assertEquals(500, $response->status());
    }
}