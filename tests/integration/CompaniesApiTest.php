<?php

use Laravel\Lumen\Testing\DatabaseMigrations;

use WA\DataStore\Company\Company;

class CompaniesTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * A basic functional test for Company
     */
    public function testGetCompanies()
    {
        factory(\WA\DataStore\Company\Company::class, 40)->create();

        $this->json('GET', 'companies')
            ->seeJsonStructure([
                'data' => [
                    0 => [ 
                        'type',
                        'id',
                        'attributes' => [
                            'name',
                            'label'
                        ],
                        'links'
                    ]
                ]
            ]);
    }

    public function testGetByCompanyId()
    {
        $company = factory(\WA\DataStore\Company\Company::class)->create();

        $this->json('GET', 'companies/'.$company->id)
            ->seeJson([
                'type' => 'companies',
                'id' => "$company->id" ,
                'name' => $company->name,
                'label' => $company->label,
            ]);

    }

    public function testRelationshipWithPages()
    {
        $this->markTestIncomplete(
          'TODO: needs to be reviewed.' 
        );

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

    }
}