<?php

use Laravel\Lumen\Testing\DatabaseTransactions;

class CompaniesTest extends TestCase
{

    use DatabaseTransactions;

    /**
     * A basic functional test for company endpoints
     *
     *
     */
    public function testGetCompanies()
    {

        $company = factory(\WA\DataStore\Company\Company::class)->create();

        $this->get('/companies')
            ->seeJsonStructure([
                'data' => [
                    0 => [ 'type','id',
                            'attributes' => [
                                'name', 'label'
                             ],
                            'links'
                    ]

                ]

         ]);

    }

    public function testGetByCompanyId()
    {
        $company = factory(\WA\DataStore\Company\Company::class)->create();

        $this->get('/companies/'.$company->id)
            ->seeJson([
                'type' => 'companies',
                'id' => "$company->id" ,
                'name' => $company->name,
                'label' => $company->label,
            ]);

    }

   /* public function testRelationshipWithPages()
    {

        $company = factory(\WA\DataStore\Company\Company::class)->create();
             

        $page = factory(\WA\DataStore\Page\Page::class)->create();

         $this->put('/pages/'.$page->id, [
            'title' => $page->title,
            'section' => $page->section,
            'content' => $page->content,
            'active' => $page->active,
            'owner_type' => 'company',
            'owner_id' => $company->id,
        ]);


        $this->get('/companies/'.$company->id.'?include=pages')
            ->seeJson([
                'type' => 'pages',
                'id' => "$page->id",
                "title" => $page->title,
                'section' => $page->section,
                'content' => $page->content,
                'active' => $page->active,
                'owner_type' => 'company',
                'owner_id' => $company->id,

            ]);

    }*/


}