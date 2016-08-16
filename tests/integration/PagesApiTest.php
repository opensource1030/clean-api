<?php

use Laravel\Lumen\Testing\DatabaseTransactions;

class PagesApiTest extends TestCase
{
    use DatabaseTransactions;     
     

    /**
     * A basic functional test for pages
     *
     *
     */

 public function testGetPages()
   {
       $page = factory(\WA\DataStore\Page\Page::class)->create();
       
       $this->get('/pages')
           ->seeJsonStructure([
               'data' => [
                   0 => [ 'type','id',
                       'attributes' => [
                           'title', 'section', 'content', 'active', 'owner_type', 'owner_id'
                       ],
                       'links'
                   ]

               ]

           ]);
   }

 public function testGetPageById()
    {


        $page = factory(\WA\DataStore\Page\Page::class)->create();     
             

        $this->get('/pages/'. $page->id)
            ->seeJson([
                'type' => 'pages',
                'title'=> $page->title,
                'section' => $page->section,
                'content' => $page->content,
                'active' => $page->active,
                'owner_type' => $page->owner_type,
                'owner_id' => $page->owner_id,

            ]);
    }


    public function testCreatePages()
    {
        $this->post('/pages',
            [
                'title' => 'Test Title',
                'section' => 'Test Section',
                'content' => 'Test Content',
                'active' => 1,
                'owner_type' => 'company',
                'owner_id' => 9,
            ])
            ->seeJson([
                'type' => 'pages',
                'title' => 'Test Title',
                'section' => 'Test Section',
                'content' => 'Test Content',
                'active' => 1,
                'owner_type' => 'company',
                'owner_id' => 9,
            ]);
    }

    public function testUpdatePages()
    {
        $page = factory(\WA\DataStore\Page\Page::class)->create();

        $this->put('/pages/'.$page->id, [
            "title" => "Test Update",
            'section' => $page->section,
            'content' => $page->content,
            'active' => $page->active,
            'owner_type' => $page->owner_type,
            'owner_id' => $page->owner_id,
        ])
            ->seeJson([
                'type' => 'pages',
                'title'=> 'Test Update',
                'section' => $page->section,
                'content' => $page->content,
                'active' => $page->active,
                'owner_type' => $page->owner_type,
                'owner_id' => $page->owner_id,
            ]);

    }

    public function testDeletePage()
    {
        $page = factory(\WA\DataStore\Page\Page::class)->create();
        $this->delete('/pages/'. $page->id);
        $response = $this->call('GET', '/pages/'.$page->id);
        $this->assertEquals(500, $response->status());

    }


}
