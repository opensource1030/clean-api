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


}