<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use WA\DataStore\Company\Company;

class CompaniesTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * A basic functional test for Company.
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
                            'label', 
                            'name',             
                            'label',            
                            'active',           
                            'udlpath' ,         
                            'isCensus' ,        
                            'udlPathRule',      
                            'assetPath' ,      
                            'shortName' ,       
                            'currentBillMonth' ,
                            'defaultLocation'  
                        ],
                        'links',
                    ],
                ],
            ]);
    }

    public function testGetByCompanyId()
    {

        $company = factory(\WA\DataStore\Company\Company::class)->create();

        $this->json('GET', 'companies/'.$company->id)
            ->seeJson([
                'type' => 'companies',
                'id' => "$company->id",
                'name' => $company->name,
                'label' => $company->label,
            ]);
    }

    public function testGetCompanyByIdandIncludesCurrentBillMonths()
    {
        $company = factory(\WA\DataStore\Company\Company::class)->create();

        $currentBillMonth1 = factory(\WA\DataStore\Company\CompanyCurrentBillMonth::class)->create();

        $company->currentBillMonths()->save($currentBillMonth1);

        $this->get('/companies/'.$company->id.'?include=currentBillMonths')
            ->seeJsonStructure([
                'data' => [
                    'type',
                    'id',
                    'attributes' => [
                        'name',
                        'label',
                        'active',
                        'udlpath',
                        'isCensus',
                        'udlPathRule',
                        'assetPath',
                        'currentBillMonth',
                    ],
                    'links' => [
                        'self',
                    ],
                    'relationships' => [
                        'currentBillMonths' => [
                            'links' => [
                                'self',
                                'related',
                            ],
                            'data' => [
                                0 => [
                                    'type',
                                    'id',
                                ],
                            ],
                        ],
                    ],
                ],
                'included' => [
                    0 => [
                        'type',
                        'id',
                        'attributes' => [
                            'bill_month',
                            'carrier',
                            'companyId',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],

                ],
            ]);
    }

    public function testCreateCompany()
    {
        $company = factory(\WA\DataStore\Company\Company::class)->create();

         $this->json('POST', 'companies',
            [
                'data' => [
                    'type'=> 'companies',
                    'attributes' => [
                        'name'             => 'SirionDev',
                        'label'            => 'Sirion',
                        'active'           => 1,
                        'udlpath'          => null,
                        'isCensus'         => 0,
                        'udlPathRule'      => null,
                        'assetPath'        => '/var/www/clean/storage/clients/clients/acme',
                        'shortName'        => 'ShortName',
                        'currentBillMonth' => $company->currentBillMonth,
                        'defaultLocation'  => $company->defaultLocation
                    ],
                ]
            ]
            )->seeJson(
            [
                'type' => 'companies',
                'name' => 'SirionDev',
                'label'=> 'Sirion',
                'active'           => 1,
                'udlpath'          => null,
                'isCensus'         => 0,
                'udlPathRule'      => null,
                'assetPath'        => '/var/www/clean/storage/clients/clients/acme',
                'shortName'        => 'ShortName',
                'currentBillMonth' => $company->currentBillMonth,
                'defaultLocation'  => $company->defaultLocation
            ]);
    }
    public function testCreateCompanyReturnNoValidData()
    {
        // 'data' no valid.
        $this->json('POST', 'companies',
            [
                'NoValid' => [
                    ],
            ]
            )->seeJson(
            [
                'errors' => [
                    'json' => 'JSON is Invalid',
                ],
            ]
        );
    }

    public function testCreateCompanyReturnNoValidType()
    {
        // 'type' no valid.
        $this->json('POST', 'companies',
            [
                'data' => [
                    'NoValid' => 'companies',
                    'attributes' => [
                        'name' => 'SirionDev',
                    ],
                ],

            ]
            )->seeJson(
            [
                'errors' => [
                    'json' => 'JSON is Invalid',
                ],
            ]
        );
    }

    public function testCreateCompanyReturnNoValidAttributes()
    {
        // 'attributes' no valid.
        $this->json('POST', 'companies',
            [
                'data' => [
                    'type' => 'companies',
                    'NoValid' => [
                        'name' => 'SirionDev',
                    ],
                ],
            ]
            )->seeJson(
            [
                'errors' => [
                    'json' => 'JSON is Invalid',
                ],
            ]
        );
    }
    public function testUpdateCompany()
    {
        $company = factory(\WA\DataStore\Company\Company::class)->create(
            ['name' => 'nameCompany1']
        );
        $companyAux = factory(\WA\DataStore\Company\Company::class)->create(
            ['name' => 'nameCompany2']
        );
        $this->assertNotEquals($company->id, $companyAux->id);
        $this->assertNotEquals($company->name, $companyAux->name);

       $res= $this->json('PATCH', 'companies/'.$company->id,
            [
                'data' => [
                    'type' => 'companies',
                    'attributes' => [
                        'name'             => $companyAux->name,
                        'label'            => $companyAux->label,
                        'active'           => $companyAux->active,
                        'udlpath'          => $companyAux->udlpath,
                        'isCensus'         => $companyAux->isCensus,
                        'udlPathRule'      => $companyAux->udlPathRule,
                        'assetPath'        => $companyAux->assetPath,
                        'shortName'        => $companyAux->shortName,
                        'currentBillMonth' => $companyAux->currentBillMonth,
                        'defaultLocation'  => $companyAux->defaultLocation
                    ],
                ],
            ])
            ->seeJson(
            [
                'type'             => 'companies',
                'name'             => $companyAux->name,
                'label'            => $companyAux->label,
                'active'           => $companyAux->active,
                'udlpath'          => $companyAux->udlpath,
                'isCensus'         => $companyAux->isCensus,
                'udlPathRule'      => $companyAux->udlPathRule,
                'assetPath'        => $companyAux->assetPath,
                'shortName'        => $companyAux->shortName,
                'currentBillMonth' => $companyAux->currentBillMonth,
                'defaultLocation'  => $companyAux->defaultLocation
            ]);
            Log::debug("RES TEST: ".print_r($res->response->getContent(), true));
    }

    public function testDeleteCompanyIfExists()
    {
        // CREATE & DELETE
        $company = factory(\WA\DataStore\Company\Company::class)->create();
        $responseDel = $this->call('DELETE', 'companies/'.$company->id);
        $this->assertEquals(200, $responseDel->status());
        $responseGet = $this->call('GET', 'companies/'.$company->id);
        $this->assertEquals(404, $responseGet->status());
    }

    public function testDeletecompanyIfNoExists()
    {
        // DELETE NO EXISTING.
        $responseDel = $this->call('DELETE', 'companies/1');
        $this->assertEquals(404, $responseDel->status());
    }

}
