<?php

class CategoryAppsApiTest extends \TestCase
{
    use \Laravel\Lumen\Testing\DatabaseMigrations;

    public function testGetCategoryApps()
    {
        factory(\WA\DataStore\Category\CategoryApp::class, 40)->create();

        $this->json('GET', 'categoryapps')
            ->seeJsonStructure([
                'data' => [
                    0 => [
                        'type',
                        'id',
                        'attributes' => [
                            'name'
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
                ],
            ]);
    }

    public function testGetAddressById()
    {
        $categoryApp = factory(\WA\DataStore\Category\CategoryApp::class)->create();

        $res = $this->json('GET', 'categoryapps/'.$categoryApp->id)
            ->seeJson([
                'type' => 'categoryapps',
                'name' => $categoryApp->name
            ]);
    }

    public function testCreateAddress()
    {
        $this->json('POST', 'categoryapps',
            [
                'data' => [
                    'type' => 'categoryapps',
                    'attributes' => [
                        'name' => 'addressName'
                    ],
                ],
            ])
            ->seeJson([
                'type' => 'categoryapps',
                'name' => 'addressName'
            ]);
    }

    public function testUpdateAddress()
    {
        $categoryApp1 = factory(\WA\DataStore\Category\CategoryApp::class)->create([
            'name' => 'name1'
        ]);
        $categoryApp2 = factory(\WA\DataStore\Category\CategoryApp::class)->create([
            'name' => 'name2'
        ]);

        $this->assertNotEquals($categoryApp1->id, $categoryApp2->id);
        $this->assertNotEquals($categoryApp1->name, $categoryApp2->name);

        $this->json('GET', 'categoryapps/'.$categoryApp1->id)
            ->seeJson([
                'type' => 'categoryapps',
                'name' => $categoryApp1->name
            ]);

        $this->json('PATCH', 'categoryapps/'.$categoryApp1->id,
            [
                'data' => [
                    'type' => 'categoryapps',
                    'attributes' => [
                        'name' => $categoryApp2->name
                    ],
                ],
            ])
            ->seeJson([
                'id' => $categoryApp1->id,
                'name' => $categoryApp2->name
            ]);
    }

    public function testDeleteAddressIfExists()
    {
        $categoryApp = factory(\WA\DataStore\Category\CategoryApp::class)->create();
        $responseDel = $this->call('DELETE', 'categoryapps/'.$categoryApp->id);
        $this->assertEquals(200, $responseDel->status());
        $responseGet = $this->call('GET', 'categoryapps/'.$categoryApp->id);
        $this->assertEquals(404, $responseGet->status());
    }

    public function testDeleteAddressIfNoExists()
    {
        $responseDel = $this->call('DELETE', 'categoryapps/1');
        $this->assertEquals(404, $responseDel->status());
    }
}
