<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use WA\DataStore\Company\Company;
use WA\DataStore\Company\CompanyUserImportJob;
use WA\DataStore\User\User;

class CompaniesTest extends \TestCase
{
    use \Laravel\Lumen\Testing\DatabaseMigrations;

    public function testGetCompanies()
    {
        factory(\WA\DataStore\Company\Company::class, 40)->create();

        $res = $this->json('GET', 'companies')
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
                    ]
                ]
            ]);
    }

    public function testGetByCompanyId()
    {
        $company = factory(\WA\DataStore\Company\Company::class)->create();

        $response = $this->json('GET', 'companies/'.$company->id)
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
                        ]
                    ]
                ]
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

    public function testCreateCompanyIncludeUdls()
    {
        $company = factory(\WA\DataStore\Company\Company::class)->create();

        $this->json('POST', 'companies?include=udls,udls.udlvalues',
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
                    'relationships' => [
                        'udls' => [
                            'data' => [
                                [
                                    'type' => 'udls',
                                    'id'  => 0,
                                    'attributes' => [
                                        'name' => 'Udl Test 1',
                                        'inputType' => 'string'
                                    ],
                                    'relationships' => [
                                        'udlvalues' => [
                                            'data' => [
                                                ['type' => 'udlvalues', 'id' => 0, 'name' => 'Udl1 Value1'],
                                                ['type' => 'udlvalues', 'id' => 0, 'name' => 'Udl1 Value2'],
                                                ['type' => 'udlvalues', 'id' => 0, 'name' => 'Udl1 Value3'],
                                                ['type' => 'udlvalues', 'id' => 0, 'name' => 'Udl1 Value4'],
                                                ['type' => 'udlvalues', 'id' => 0, 'name' => 'Udl1 Value5']
                                            ]
                                        ]
                                    ]
                                ],
                                [
                                    'type' => 'udls',
                                    'id'  => 0,
                                    'attributes' => [
                                        'name' => 'Udl Test 2',
                                        'inputType' => 'string'
                                    ],
                                    'relationships' => [
                                        'udlvalues' => [
                                            'data' => [
                                                ['type' => 'udlvalues', 'id' => 0, 'name' => 'Udl2 Value1'],
                                                ['type' => 'udlvalues', 'id' => 0, 'name' => 'Udl2 Value2'],
                                                ['type' => 'udlvalues', 'id' => 0, 'name' => 'Udl2 Value3'],
                                                ['type' => 'udlvalues', 'id' => 0, 'name' => 'Udl2 Value4'],
                                                ['type' => 'udlvalues', 'id' => 0, 'name' => 'Udl2 Value5']
                                            ]
                                        ]
                                    ]
                                ],
                                [
                                    'type' => 'udls',
                                    'id'  => 0,
                                    'attributes' => [
                                        'name' => 'Udl Test 3',
                                        'inputType' => 'string'
                                    ],
                                    'relationships' => [
                                        'udlvalues' => [
                                            'data' => [
                                                ['type' => 'udlvalues', 'id' => 0, 'name' => 'Udl3 Value1'],
                                                ['type' => 'udlvalues', 'id' => 0, 'name' => 'Udl3 Value2'],
                                                ['type' => 'udlvalues', 'id' => 0, 'name' => 'Udl3 Value3'],
                                                ['type' => 'udlvalues', 'id' => 0, 'name' => 'Udl3 Value4'],
                                                ['type' => 'udlvalues', 'id' => 0, 'name' => 'Udl3 Value5']
                                            ]
                                        ]
                                    ]
                                ],
                                [
                                    'type' => 'udls',
                                    'id'  => 0,
                                    'attributes' => [
                                        'name' => 'Udl Test 4',
                                        'inputType' => 'string'
                                    ],
                                    'relationships' => [
                                        'udlvalues' => [
                                            'data' => [
                                                ['type' => 'udlvalues', 'id' => 0, 'name' => 'Udl4 Value1'],
                                                ['type' => 'udlvalues', 'id' => 0, 'name' => 'Udl4 Value2'],
                                                ['type' => 'udlvalues', 'id' => 0, 'name' => 'Udl4 Value3'],
                                                ['type' => 'udlvalues', 'id' => 0, 'name' => 'Udl4 Value4'],
                                                ['type' => 'udlvalues', 'id' => 0, 'name' => 'Udl4 Value5']
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
            //Log::debug('testCreateCompanyIncludeUdls: '.print_r($res->response->getContent(), true));
            )->seeJson(
            [
                'type'              => 'companies',
                'name'              => 'SirionDev',
                'label'             => 'Sirion',
                'active'            => 1,
                'udlpath'           => null,
                'isCensus'          => 0,
                'udlPathRule'       => null,
                'assetPath'         => '/var/www/clean/storage/clients/clients/acme',
                'shortName'         => 'ShortName',
                'currentBillMonth'  => $company->currentBillMonth,
                'defaultLocation'   => $company->defaultLocation
            ]
            )->seeJsonStructure(
            [
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
                        'udls' => [
                            'links' => [
                                'self',
                                'related',
                            ],
                            'data' => [
                                0 => [
                                    'type',
                                    'id',
                                ],
                                1 => [
                                    'type',
                                    'id',
                                ],
                                2 => [
                                    'type',
                                    'id',
                                ],
                                3 => [
                                    'type',
                                    'id',
                                ]
                            ]
                        ]
                    ]
                ],
                'included' => [
                    0 => [ // UDLVALUES
                        'type',
                        'id',
                        'attributes' => [
                            'udlId',
                            'udlName',
                            'udlValue',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                    1 => [ // UDLVALUES
                        'type',
                        'id',
                        'attributes' => [
                            'udlId',
                            'udlName',
                            'udlValue',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                    2 => [ // UDLVALUES
                        'type',
                        'id',
                        'attributes' => [
                            'udlId',
                            'udlName',
                            'udlValue',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                    3 => [ // UDLVALUES
                        'type',
                        'id',
                        'attributes' => [
                            'udlId',
                            'udlName',
                            'udlValue',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                    4 => [ // UDLVALUES
                        'type',
                        'id',
                        'attributes' => [
                            'udlId',
                            'udlName',
                            'udlValue',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                    5 => [ // UDLVALUES
                        'type',
                        'id',
                        'attributes' => [
                            'udlId',
                            'udlName',
                            'udlValue',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                    6 => [ // UDLVALUES
                        'type',
                        'id',
                        'attributes' => [
                            'udlId',
                            'udlName',
                            'udlValue',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                    7 => [ // UDLVALUES
                        'type',
                        'id',
                        'attributes' => [
                            'udlId',
                            'udlName',
                            'udlValue',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                    8 => [ // UDLVALUES
                        'type',
                        'id',
                        'attributes' => [
                            'udlId',
                            'udlName',
                            'udlValue',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                    9 => [ // UDLVALUES
                        'type',
                        'id',
                        'attributes' => [
                            'udlId',
                            'udlName',
                            'udlValue',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                    10 => [ // UDLVALUES
                        'type',
                        'id',
                        'attributes' => [
                            'udlId',
                            'udlName',
                            'udlValue',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                    11 => [ // UDLVALUES
                        'type',
                        'id',
                        'attributes' => [
                            'udlId',
                            'udlName',
                            'udlValue',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                    12 => [ // UDLVALUES
                        'type',
                        'id',
                        'attributes' => [
                            'udlId',
                            'udlName',
                            'udlValue',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                    13 => [ // UDLVALUES
                        'type',
                        'id',
                        'attributes' => [
                            'udlId',
                            'udlName',
                            'udlValue',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                    14 => [ // UDLVALUES
                        'type',
                        'id',
                        'attributes' => [
                            'udlId',
                            'udlName',
                            'udlValue',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                    15 => [ // UDLVALUES
                        'type',
                        'id',
                        'attributes' => [
                            'udlId',
                            'udlName',
                            'udlValue',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                    16 => [ // UDLVALUES
                        'type',
                        'id',
                        'attributes' => [
                            'udlId',
                            'udlName',
                            'udlValue',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                    17 => [ // UDLVALUES
                        'type',
                        'id',
                        'attributes' => [
                            'udlId',
                            'udlName',
                            'udlValue',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                    18 => [ // UDLVALUES
                        'type',
                        'id',
                        'attributes' => [
                            'udlId',
                            'udlName',
                            'udlValue',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                    19 => [ // UDLVALUES
                        'type',
                        'id',
                        'attributes' => [
                            'udlId',
                            'udlName',
                            'udlValue',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                    20 => [ // UDLS
                        'type',
                        'id',
                        'attributes' => [
                            'companyId',
                            'name',
                            'inputType',
                            'legacyUdlField'
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                    21 => [ // UDLS
                        'type',
                        'id',
                        'attributes' => [
                            'companyId',
                            'name',
                            'inputType',
                            'legacyUdlField'
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                    22 => [ // UDLS
                        'type',
                        'id',
                        'attributes' => [
                            'companyId',
                            'name',
                            'inputType',
                            'legacyUdlField'
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                    23 => [ // UDLS
                        'type',
                        'id',
                        'attributes' => [
                            'companyId',
                            'name',
                            'inputType',
                            'legacyUdlField'
                        ],
                        'links' => [
                            'self',
                        ]
                    ]
                ]
            ]);
    }

    public function testCreateCompanyIncludeAddress()
    {
        $company = factory(\WA\DataStore\Company\Company::class)->create();
        $address1 = factory(\WA\DataStore\Address\Address::class)->create();

        $this->json('POST', 'companies?include=addresses',
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
                    'relationships' => [
                        'addresses' => [
                            'data' => [
                                [
                                    'type' => 'addresses',
                                    'id'  => $address1->id
                                ],
                                [
                                    'type' => 'addresses',
                                    'id'  => 0,
                                    'attributes' => [
                                        'name' => 'Drug Store 01',
                                        'attn' => '',
                                        'phone' => '',
                                        'address' => 'C/huesca 01',
                                        'city' => 'El Grado',
                                        'state' => 'Huesca',
                                        'country' => 'Spain',
                                        'postalCode' => '22390'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
            //Log::debug('testCreateCompanyIncludeAddress: '.print_r($res->response->getContent(), true));
            )->seeJson(
            [
                'type'              => 'companies',
                'name'              => 'SirionDev',
                'label'             => 'Sirion',
                'active'            => 1,
                'udlpath'           => null,
                'isCensus'          => 0,
                'udlPathRule'       => null,
                'assetPath'         => '/var/www/clean/storage/clients/clients/acme',
                'shortName'         => 'ShortName',
                'currentBillMonth'  => $company->currentBillMonth,
                'defaultLocation'   => $company->defaultLocation
            ]
            )->seeJsonStructure(
            [
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
                        'addresses' => [
                            'links' => [
                                'self',
                                'related',
                            ],
                            'data' => [
                                0 => [
                                    'type',
                                    'id',
                                ],
                                1 => [
                                    'type',
                                    'id',
                                ]
                            ]
                        ]
                    ]
                ],
                'included' => [
                    0 => [ // ADDRESS
                        'type',
                        'id',
                        'attributes' => [
                            'name',
                            'attn',
                            'phone',
                            'address',
                            'city',
                            'state',
                            'country',
                            'postalCode',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                    1 => [ // ADDRESS
                        'type',
                        'id',
                        'attributes' => [
                            'name',
                            'attn',
                            'phone',
                            'address',
                            'city',
                            'state',
                            'country',
                            'postalCode',
                        ],
                        'links' => [
                            'self',
                        ],
                    ], 
                ]
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
    }

    public function testUpdateCompanyIncludeUdls()
    {
        $company = factory(\WA\DataStore\Company\Company::class)->create();

        $this->json('PATCH', 'companies/'.$company->id.'?include=udls,udls.udlvalues',
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
                    'relationships' => [
                        'udls' => [
                            'data' => [
                                [
                                    'type' => 'udls',
                                    'id'  => 0,
                                    'attributes' => [
                                        'name' => 'Udl Test 1',
                                        'inputType' => 'string'
                                    ],
                                    'relationships' => [
                                        'udlvalues' => [
                                            'data' => [
                                                ['type' => 'udlvalues', 'id' => 0, 'name' => 'Udl1 Value1'],
                                                ['type' => 'udlvalues', 'id' => 0, 'name' => 'Udl1 Value2'],
                                                ['type' => 'udlvalues', 'id' => 0, 'name' => 'Udl1 Value3'],
                                                ['type' => 'udlvalues', 'id' => 0, 'name' => 'Udl1 Value4'],
                                                ['type' => 'udlvalues', 'id' => 0, 'name' => 'Udl1 Value5']
                                            ]
                                        ]
                                    ]
                                ],
                                [
                                    'type' => 'udls',
                                    'id'  => 0,
                                    'attributes' => [
                                        'name' => 'Udl Test 2',
                                        'inputType' => 'string'
                                    ],
                                    'relationships' => [
                                        'udlvalues' => [
                                            'data' => [
                                                ['type' => 'udlvalues', 'id' => 0, 'name' => 'Udl2 Value1'],
                                                ['type' => 'udlvalues', 'id' => 0, 'name' => 'Udl2 Value2'],
                                                ['type' => 'udlvalues', 'id' => 0, 'name' => 'Udl2 Value3'],
                                                ['type' => 'udlvalues', 'id' => 0, 'name' => 'Udl2 Value4'],
                                                ['type' => 'udlvalues', 'id' => 0, 'name' => 'Udl2 Value5']
                                            ]
                                        ]
                                    ]
                                ],
                                [
                                    'type' => 'udls',
                                    'id'  => 0,
                                    'attributes' => [
                                        'name' => 'Udl Test 3',
                                        'inputType' => 'string'
                                    ],
                                    'relationships' => [
                                        'udlvalues' => [
                                            'data' => [
                                                ['type' => 'udlvalues', 'id' => 0, 'name' => 'Udl3 Value1'],
                                                ['type' => 'udlvalues', 'id' => 0, 'name' => 'Udl3 Value2'],
                                                ['type' => 'udlvalues', 'id' => 0, 'name' => 'Udl3 Value3'],
                                                ['type' => 'udlvalues', 'id' => 0, 'name' => 'Udl3 Value4'],
                                                ['type' => 'udlvalues', 'id' => 0, 'name' => 'Udl3 Value5']
                                            ]
                                        ]
                                    ]
                                ],
                                [
                                    'type' => 'udls',
                                    'id'  => 0,
                                    'attributes' => [
                                        'name' => 'Udl Test 4',
                                        'inputType' => 'string'
                                    ],
                                    'relationships' => [
                                        'udlvalues' => [
                                            'data' => [
                                                ['type' => 'udlvalues', 'id' => 0, 'name' => 'Udl4 Value1'],
                                                ['type' => 'udlvalues', 'id' => 0, 'name' => 'Udl4 Value2'],
                                                ['type' => 'udlvalues', 'id' => 0, 'name' => 'Udl4 Value3'],
                                                ['type' => 'udlvalues', 'id' => 0, 'name' => 'Udl4 Value4'],
                                                ['type' => 'udlvalues', 'id' => 0, 'name' => 'Udl4 Value5']
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ])
            //Log::debug('testCreateCompanyIncludeUdls: '.print_r($res->response->getContent(), true));
            ->seeJson(
            [
                'type'              => 'companies',
                'name'              => 'SirionDev',
                'label'             => 'Sirion',
                'active'            => 1,
                'udlpath'           => null,
                'isCensus'          => 0,
                'udlPathRule'       => null,
                'assetPath'         => '/var/www/clean/storage/clients/clients/acme',
                'shortName'         => 'ShortName',
                'currentBillMonth'  => $company->currentBillMonth,
                'defaultLocation'   => $company->defaultLocation
            ]
            )->seeJsonStructure(
            [
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
                        'udls' => [
                            'links' => [
                                'self',
                                'related',
                            ],
                            'data' => [
                                0 => [
                                    'type',
                                    'id',
                                ],
                                1 => [
                                    'type',
                                    'id',
                                ],
                                2 => [
                                    'type',
                                    'id',
                                ],
                                3 => [
                                    'type',
                                    'id',
                                ]
                            ],
                        ],
                    ],
                ],
                'included' => [
                    0 => [ // UDLVALUES
                        'type',
                        'id',
                        'attributes' => [
                            'udlId',
                            'udlName',
                            'udlValue',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                    1 => [ // UDLVALUES
                        'type',
                        'id',
                        'attributes' => [
                            'udlId',
                            'udlName',
                            'udlValue',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                    2 => [ // UDLVALUES
                        'type',
                        'id',
                        'attributes' => [
                            'udlId',
                            'udlName',
                            'udlValue',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                    3 => [ // UDLVALUES
                        'type',
                        'id',
                        'attributes' => [
                            'udlId',
                            'udlName',
                            'udlValue',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                    4 => [ // UDLVALUES
                        'type',
                        'id',
                        'attributes' => [
                            'udlId',
                            'udlName',
                            'udlValue',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                    5 => [ // UDLVALUES
                        'type',
                        'id',
                        'attributes' => [
                            'udlId',
                            'udlName',
                            'udlValue',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                    6 => [ // UDLVALUES
                        'type',
                        'id',
                        'attributes' => [
                            'udlId',
                            'udlName',
                            'udlValue',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                    7 => [ // UDLVALUES
                        'type',
                        'id',
                        'attributes' => [
                            'udlId',
                            'udlName',
                            'udlValue',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                    8 => [ // UDLVALUES
                        'type',
                        'id',
                        'attributes' => [
                            'udlId',
                            'udlName',
                            'udlValue',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                    9 => [ // UDLVALUES
                        'type',
                        'id',
                        'attributes' => [
                            'udlId',
                            'udlName',
                            'udlValue',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                    10 => [ // UDLVALUES
                        'type',
                        'id',
                        'attributes' => [
                            'udlId',
                            'udlName',
                            'udlValue',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                    11 => [ // UDLVALUES
                        'type',
                        'id',
                        'attributes' => [
                            'udlId',
                            'udlName',
                            'udlValue',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                    12 => [ // UDLVALUES
                        'type',
                        'id',
                        'attributes' => [
                            'udlId',
                            'udlName',
                            'udlValue',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                    13 => [ // UDLVALUES
                        'type',
                        'id',
                        'attributes' => [
                            'udlId',
                            'udlName',
                            'udlValue',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                    14 => [ // UDLVALUES
                        'type',
                        'id',
                        'attributes' => [
                            'udlId',
                            'udlName',
                            'udlValue',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                    15 => [ // UDLVALUES
                        'type',
                        'id',
                        'attributes' => [
                            'udlId',
                            'udlName',
                            'udlValue',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                    16 => [ // UDLVALUES
                        'type',
                        'id',
                        'attributes' => [
                            'udlId',
                            'udlName',
                            'udlValue',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                    17 => [ // UDLVALUES
                        'type',
                        'id',
                        'attributes' => [
                            'udlId',
                            'udlName',
                            'udlValue',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                    18 => [ // UDLVALUES
                        'type',
                        'id',
                        'attributes' => [
                            'udlId',
                            'udlName',
                            'udlValue',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                    19 => [ // UDLVALUES
                        'type',
                        'id',
                        'attributes' => [
                            'udlId',
                            'udlName',
                            'udlValue',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                    20 => [ // UDLS
                        'type',
                        'id',
                        'attributes' => [
                            'companyId',
                            'name',
                            'inputType',
                            'legacyUdlField'
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                    21 => [ // UDLS
                        'type',
                        'id',
                        'attributes' => [
                            'companyId',
                            'name',
                            'inputType',
                            'legacyUdlField'
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                    22 => [ // UDLS
                        'type',
                        'id',
                        'attributes' => [
                            'companyId',
                            'name',
                            'inputType',
                            'legacyUdlField'
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                    23 => [ // UDLS
                        'type',
                        'id',
                        'attributes' => [
                            'companyId',
                            'name',
                            'inputType',
                            'legacyUdlField'
                        ],
                        'links' => [
                            'self',
                        ]
                    ]
                ]
            ]);
    }

    public function testUpdateCompanyIncludeAddress()
    {
        $company = factory(\WA\DataStore\Company\Company::class)->create();

        $address1 = factory(\WA\DataStore\Address\Address::class)->create();
        $company->addresses()->sync([$address1->id]);

        $address1DB = DB::table('company_address')->where('companyId', $company->id)->get();
        $this->assertCount(1, $address1DB);
        $this->assertEquals($address1->id , $address1DB[0]->id);

        $address2 = factory(\WA\DataStore\Address\Address::class)->create();
        $address3 = factory(\WA\DataStore\Address\Address::class)->create();

        $this->json('PATCH', 'companies/'.$company->id.'?include=addresses',
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
                    'relationships' => [
                        'addresses' => [
                            'data' => [
                                [
                                    'type' => 'addresses',
                                    'id'  => 0,
                                    'attributes' => [
                                        'name' => $address2->name,
                                        'attn' => $address2->attn,
                                        'phone' => $address2->phone,
                                        'address' => $address2->address,
                                        'city' => $address2->city,
                                        'state' => $address2->state,
                                        'country' => $address2->country,
                                        'postalCode' => $address2->postalCode
                                    ]
                                ],
                                [
                                    'type' => 'addresses',
                                    'id'  => $address3->id
                                ]
                            ]
                        ]
                    ]
                ]
            ])
            //Log::debug('testCreateCompanyIncludeAddress: '.print_r($res->response->getContent(), true));
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
                        'shortName',
                        'defaultLocation'
                    ],
                    'links' => [
                        'self'
                    ],
                    'relationships' => [
                        'addresses' => [
                            'links' => [
                                'self',
                                'related',
                            ],
                            'data' => [
                                0 => [
                                    'type',
                                    'id'
                                ],
                                1 => [
                                    'type',
                                    'id'
                                ]
                            ]
                        ]
                    ]
                ],
                'included' => [
                    0 => [
                        'type',
                        'id',
                        'attributes' => [
                            'name',
                            'attn',
                            'phone',
                            'address',
                            'city',
                            'state',
                            'country',
                            'postalCode'
                        ],
                        'links' => [
                            'self'
                        ],
                    ],
                    1 => [
                        'type',
                        'id',
                        'attributes' => [
                            'name',
                            'attn',
                            'phone',
                            'address',
                            'city',
                            'state',
                            'country',
                            'postalCode'
                        ],
                        'links' => [
                            'self'
                        ],
                    ]
                ]
            ]);

        $address2DB = DB::table('company_address')->where('companyId', $company->id)->get();
        $this->assertCount(2, $address2DB);
        $this->assertEquals($address2->id , $address2DB[0]->id);
        $this->assertEquals($address3->id , $address2DB[1]->id);
    }

    public function testUpdateCompanyIncludeUdlsAddOneUdl()
    {
        $company = factory(\WA\DataStore\Company\Company::class)->create();

        $udl = factory(\WA\DataStore\Udl\Udl::class)->create(['companyId' => $company->id]);

        $udlvalue1 = factory(\WA\DataStore\UdlValue\UdlValue::class)->create(['udlId' => $udl->id]);
        $udlvalue2 = factory(\WA\DataStore\UdlValue\UdlValue::class)->create(['udlId' => $udl->id]);
        $udlvalue3 = factory(\WA\DataStore\UdlValue\UdlValue::class)->create(['udlId' => $udl->id]);

        $udls = DB::table('udls')->where('companyId', $company->id)->get();
        $this->assertCount(1, $udls);
        $this->assertEquals($udls[0]->id, $udl->id);

        $udlvalues = DB::table('udl_values')->where('udlId', $udl->id)->orderBy('id')->get();
        $this->assertCount(3, $udlvalues);
        $this->assertEquals($udlvalues[0]->id, $udlvalue1->id);
        $this->assertEquals($udlvalues[1]->id, $udlvalue2->id);
        $this->assertEquals($udlvalues[2]->id, $udlvalue3->id);

        // ADD ONE UDL
        $res = $this->json('PATCH', 'companies/'.$company->id.'?include=udls,udls.udlvalues',
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
                    'relationships' => [
                        'udls' => [
                            'data' => [
                                [
                                    'type' => 'udls',
                                    'id'  => $udl->id,
                                    'attributes' => [
                                        'name' => 'Udl Test 1',
                                        'inputType' => 'string'
                                    ],
                                    'relationships' => [
                                        'udlvalues' => [
                                            'data' => [
                                                ['type' => 'udlvalues', 'id' => $udlvalue1->id, 'name' => 'Udl1 Value1'],
                                                ['type' => 'udlvalues', 'id' => $udlvalue2->id, 'name' => 'Udl1 Value2'],
                                                ['type' => 'udlvalues', 'id' => $udlvalue3->id, 'name' => 'Udl1 Value3']
                                            ]
                                        ]
                                    ]
                                ],
                                [
                                    'type' => 'udls',
                                    'id'  => 0,
                                    'attributes' => [
                                        'name' => 'Udl Create 1',
                                        'inputType' => 'string'
                                    ],
                                    'relationships' => [
                                        'udlvalues' => [
                                            'data' => [
                                                ['type' => 'udlvalues', 'id' => 0, 'name' => 'UdlX Value1'],
                                                ['type' => 'udlvalues', 'id' => 0, 'name' => 'UdlX Value2'],
                                                ['type' => 'udlvalues', 'id' => 0, 'name' => 'UdlX Value3']
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ])
            //Log::debug('testUpdateCompanyIncludeUdlsAddOneUdl: '.print_r($res->response->getContent(), true));
            ->seeJson(
            [
                'type'              => 'companies',
                'name'              => 'SirionDev',
                'label'             => 'Sirion',
                'active'            => 1,
                'udlpath'           => null,
                'isCensus'          => 0,
                'udlPathRule'       => null,
                'assetPath'         => '/var/www/clean/storage/clients/clients/acme',
                'shortName'         => 'ShortName',
                'currentBillMonth'  => $company->currentBillMonth,
                'defaultLocation'   => $company->defaultLocation
            ])
            ->seeJsonStructure(
            [
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
                        'udls' => [
                            'links' => [
                                'self',
                                'related',
                            ],
                            'data' => [
                                0 => [
                                    'type',
                                    'id',
                                ],
                                1 => [
                                    'type',
                                    'id',
                                ]
                            ],
                        ],
                    ],
                ],
                'included' => [
                    0 => [ // UDLVALUES
                        'type',
                        'id',
                        'attributes' => [
                            'udlId',
                            'udlName',
                            'udlValue',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                    1 => [ // UDLVALUES
                        'type',
                        'id',
                        'attributes' => [
                            'udlId',
                            'udlName',
                            'udlValue',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                    2 => [ // UDLVALUES
                        'type',
                        'id',
                        'attributes' => [
                            'udlId',
                            'udlName',
                            'udlValue',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                    3 => [ // UDLVALUES
                        'type',
                        'id',
                        'attributes' => [
                            'udlId',
                            'udlName',
                            'udlValue',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                    4 => [ // UDLVALUES
                        'type',
                        'id',
                        'attributes' => [
                            'udlId',
                            'udlName',
                            'udlValue',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                    5 => [ // UDLVALUES
                        'type',
                        'id',
                        'attributes' => [
                            'udlId',
                            'udlName',
                            'udlValue',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                    6 => [ // UDLS
                        'type',
                        'id',
                        'attributes' => [
                            'companyId',
                            'name',
                            'inputType',
                            'legacyUdlField'
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                    7 => [ // UDLS
                        'type',
                        'id',
                        'attributes' => [
                            'companyId',
                            'name',
                            'inputType',
                            'legacyUdlField'
                        ],
                        'links' => [
                            'self',
                        ]
                    ]
                ]
            ]);
    }

    public function testUpdateCompanyIncludeUdlsDeleteOneUdl()
    {
        $company = factory(\WA\DataStore\Company\Company::class)->create();

        $udl1 = factory(\WA\DataStore\Udl\Udl::class)->create(['companyId' => $company->id]);
        $udl2 = factory(\WA\DataStore\Udl\Udl::class)->create(['companyId' => $company->id]);
        $udl3 = factory(\WA\DataStore\Udl\Udl::class)->create(['companyId' => $company->id]);

        $udl1value1 = factory(\WA\DataStore\UdlValue\UdlValue::class)->create(['udlId' => $udl1->id]);
        $udl1value2 = factory(\WA\DataStore\UdlValue\UdlValue::class)->create(['udlId' => $udl1->id]);
        $udl1value3 = factory(\WA\DataStore\UdlValue\UdlValue::class)->create(['udlId' => $udl1->id]);

        $udl2value1 = factory(\WA\DataStore\UdlValue\UdlValue::class)->create(['udlId' => $udl2->id]);
        $udl2value2 = factory(\WA\DataStore\UdlValue\UdlValue::class)->create(['udlId' => $udl2->id]);
        $udl2value3 = factory(\WA\DataStore\UdlValue\UdlValue::class)->create(['udlId' => $udl2->id]);

        $udl3value1 = factory(\WA\DataStore\UdlValue\UdlValue::class)->create(['udlId' => $udl3->id]);
        $udl3value2 = factory(\WA\DataStore\UdlValue\UdlValue::class)->create(['udlId' => $udl3->id]);
        $udl3value3 = factory(\WA\DataStore\UdlValue\UdlValue::class)->create(['udlId' => $udl3->id]);

        $udls = DB::table('udls')->where('companyId', $company->id)->orderBy('id')->get();
        $this->assertCount(3, $udls);
        $this->assertEquals($udls[0]->id, $udl1->id);
        $this->assertEquals($udls[1]->id, $udl2->id);
        $this->assertEquals($udls[2]->id, $udl3->id);

        $udl1values = DB::table('udl_values')->where('udlId', $udl1->id)->orderBy('id')->get();
        $this->assertCount(3, $udl1values);
        $this->assertEquals($udl1values[0]->id, $udl1value1->id);
        $this->assertEquals($udl1values[1]->id, $udl1value2->id);
        $this->assertEquals($udl1values[2]->id, $udl1value3->id);

        $udl2values = DB::table('udl_values')->where('udlId', $udl2->id)->orderBy('id')->get();
        $this->assertCount(3, $udl2values);
        $this->assertEquals($udl2values[0]->id, $udl2value1->id);
        $this->assertEquals($udl2values[1]->id, $udl2value2->id);
        $this->assertEquals($udl2values[2]->id, $udl2value3->id);

        $udl3values = DB::table('udl_values')->where('udlId', $udl3->id)->orderBy('id')->get();
        $this->assertCount(3, $udl3values);
        $this->assertEquals($udl3values[0]->id, $udl3value1->id);
        $this->assertEquals($udl3values[1]->id, $udl3value2->id);
        $this->assertEquals($udl3values[2]->id, $udl3value3->id);

        // ADD ONE UDL
        $res = $this->json('PATCH', 'companies/'.$company->id.'?include=udls,udls.udlvalues',
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
                    'relationships' => [
                        'udls' => [
                            'data' => [
                                [
                                    'type' => 'udls',
                                    'id'  => $udl1->id,
                                    'attributes' => [
                                        'name' => 'Udl Test 1',
                                        'inputType' => 'string'
                                    ],
                                    'relationships' => [
                                        'udlvalues' => [
                                            'data' => [
                                                ['type' => 'udlvalues', 'id' => $udl1value1->id, 'name' => 'Udl1 Value1'],
                                                ['type' => 'udlvalues', 'id' => $udl1value2->id, 'name' => 'Udl1 Value2'],
                                                ['type' => 'udlvalues', 'id' => $udl1value3->id, 'name' => 'Udl1 Value3']
                                            ]
                                        ]
                                    ]
                                ],
                                [
                                    'type' => 'udls',
                                    'id'  => $udl2->id,
                                    'attributes' => [
                                        'name' => 'Udl Test 2',
                                        'inputType' => 'string'
                                    ],
                                    'relationships' => [
                                        'udlvalues' => [
                                            'data' => [
                                                ['type' => 'udlvalues', 'id' => $udl2value1->id, 'name' => 'Udl2 Value1'],
                                                ['type' => 'udlvalues', 'id' => $udl2value2->id, 'name' => 'Udl2 Value2'],
                                                ['type' => 'udlvalues', 'id' => $udl2value3->id, 'name' => 'Udl2 Value3']
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ])
            //Log::debug('testUpdateCompanyIncludeUdlsDeleteOneUdl: '.print_r($res->response->getContent(), true));
            ->seeJson(
            [
                'type'              => 'companies',
                'name'              => 'SirionDev',
                'label'             => 'Sirion',
                'active'            => 1,
                'udlpath'           => null,
                'isCensus'          => 0,
                'udlPathRule'       => null,
                'assetPath'         => '/var/www/clean/storage/clients/clients/acme',
                'shortName'         => 'ShortName',
                'currentBillMonth'  => $company->currentBillMonth,
                'defaultLocation'   => $company->defaultLocation
            ])
            ->seeJsonStructure(
            [
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
                        'udls' => [
                            'links' => [
                                'self',
                                'related',
                            ],
                            'data' => [
                                0 => [
                                    'type',
                                    'id',
                                ],
                                1 => [
                                    'type',
                                    'id',
                                ]
                            ],
                        ],
                    ],
                ],
                'included' => [
                    0 => [ // UDLVALUES
                        'type',
                        'id',
                        'attributes' => [
                            'udlId',
                            'udlName',
                            'udlValue',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                    1 => [ // UDLVALUES
                        'type',
                        'id',
                        'attributes' => [
                            'udlId',
                            'udlName',
                            'udlValue',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                    2 => [ // UDLVALUES
                        'type',
                        'id',
                        'attributes' => [
                            'udlId',
                            'udlName',
                            'udlValue',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                    3 => [ // UDLVALUES
                        'type',
                        'id',
                        'attributes' => [
                            'udlId',
                            'udlName',
                            'udlValue',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                    4 => [ // UDLVALUES
                        'type',
                        'id',
                        'attributes' => [
                            'udlId',
                            'udlName',
                            'udlValue',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                    5 => [ // UDLVALUES
                        'type',
                        'id',
                        'attributes' => [
                            'udlId',
                            'udlName',
                            'udlValue',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                    6 => [ // UDLS
                        'type',
                        'id',
                        'attributes' => [
                            'companyId',
                            'name',
                            'inputType',
                            'legacyUdlField'
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                    7 => [ // UDLS
                        'type',
                        'id',
                        'attributes' => [
                            'companyId',
                            'name',
                            'inputType',
                            'legacyUdlField'
                        ],
                        'links' => [
                            'self',
                        ]
                    ]
                ]
            ]);
    }

    public function testUpdateCompanyIncludeUdlsAddUdlValue()
    {
        $company = factory(\WA\DataStore\Company\Company::class)->create();

        $udl = factory(\WA\DataStore\Udl\Udl::class)->create(['companyId' => $company->id]);

        $udlvalue = factory(\WA\DataStore\UdlValue\UdlValue::class)->create(['udlId' => $udl->id]);

        $udls = DB::table('udls')->where('companyId', $company->id)->get();
        $this->assertCount(1, $udls);
        $this->assertEquals($udls[0]->id, $udl->id);

        $udlvalues = DB::table('udl_values')->where('udlId', $udl->id)->orderBy('id')->get();
        $this->assertCount(1, $udlvalues);
        $this->assertEquals($udlvalues[0]->id, $udlvalue->id);

        // ADD ONE UDL
        $res = $this->json('PATCH', 'companies/'.$company->id.'?include=udls,udls.udlvalues',
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
                    'relationships' => [
                        'udls' => [
                            'data' => [
                                [
                                    'type' => 'udls',
                                    'id'  => $udl->id,
                                    'attributes' => [
                                        'name' => 'Udl Test 1',
                                        'inputType' => 'string'
                                    ],
                                    'relationships' => [
                                        'udlvalues' => [
                                            'data' => [
                                                ['type' => 'udlvalues', 'id' => $udlvalue->id, 'name' => 'Udl1 Value1'],
                                                ['type' => 'udlvalues', 'id' => 0, 'name' => 'Create Value2']
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ])
            //Log::debug('testUpdateCompanyIncludeUdlsAddUdlValue: '.print_r($res->response->getContent(), true));
            ->seeJson(
            [
                'type'              => 'companies',
                'name'              => 'SirionDev',
                'label'             => 'Sirion',
                'active'            => 1,
                'udlpath'           => null,
                'isCensus'          => 0,
                'udlPathRule'       => null,
                'assetPath'         => '/var/www/clean/storage/clients/clients/acme',
                'shortName'         => 'ShortName',
                'currentBillMonth'  => $company->currentBillMonth,
                'defaultLocation'   => $company->defaultLocation
            ])
            ->seeJsonStructure(
            [
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
                        'udls' => [
                            'links' => [
                                'self',
                                'related',
                            ],
                            'data' => [
                                0 => [
                                    'type',
                                    'id',
                                ]
                            ],
                        ],
                    ],
                ],
                'included' => [
                    0 => [ // UDLVALUES
                        'type',
                        'id',
                        'attributes' => [
                            'udlId',
                            'udlName',
                            'udlValue',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                    1 => [ // UDLVALUES
                        'type',
                        'id',
                        'attributes' => [
                            'udlId',
                            'udlName',
                            'udlValue',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                    2 => [ // UDLS
                        'type',
                        'id',
                        'attributes' => [
                            'companyId',
                            'name',
                            'inputType',
                            'legacyUdlField'
                        ],
                        'links' => [
                            'self',
                        ],
                    ]
                ]
            ]);
    }

    public function testUpdateCompanyIncludeUdlsDeleteUdlValue()
    {
        $company = factory(\WA\DataStore\Company\Company::class)->create();

        $udl = factory(\WA\DataStore\Udl\Udl::class)->create(['companyId' => $company->id]);

        $udlvalue1 = factory(\WA\DataStore\UdlValue\UdlValue::class)->create(['udlId' => $udl->id]);
        $udlvalue2 = factory(\WA\DataStore\UdlValue\UdlValue::class)->create(['udlId' => $udl->id]);

        $udls = DB::table('udls')->where('companyId', $company->id)->get();
        $this->assertCount(1, $udls);
        $this->assertEquals($udls[0]->id, $udl->id);

        $udlvalues = DB::table('udl_values')->where('udlId', $udl->id)->orderBy('id')->get();
        $this->assertCount(2, $udlvalues);
        $this->assertEquals($udlvalues[0]->id, $udlvalue1->id);
        $this->assertEquals($udlvalues[1]->id, $udlvalue2->id);

        // ADD ONE UDL
        $res = $this->json('PATCH', 'companies/'.$company->id.'?include=udls,udls.udlvalues',
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
                    'relationships' => [
                        'udls' => [
                            'data' => [
                                [
                                    'type' => 'udls',
                                    'id'  => $udl->id,
                                    'attributes' => [
                                        'name' => 'Udl Test 1',
                                        'inputType' => 'string'
                                    ],
                                    'relationships' => [
                                        'udlvalues' => [
                                            'data' => [
                                                ['type' => 'udlvalues', 'id' => $udlvalue1->id, 'name' => 'Udl1 Value1']
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ])
            //Log::debug('testUpdateCompanyIncludeUdlsDeleteUdlValue: '.print_r($res->response->getContent(), true));
            ->seeJson(
            [
                'type'              => 'companies',
                'name'              => 'SirionDev',
                'label'             => 'Sirion',
                'active'            => 1,
                'udlpath'           => null,
                'isCensus'          => 0,
                'udlPathRule'       => null,
                'assetPath'         => '/var/www/clean/storage/clients/clients/acme',
                'shortName'         => 'ShortName',
                'currentBillMonth'  => $company->currentBillMonth,
                'defaultLocation'   => $company->defaultLocation
            ])
            ->seeJsonStructure(
            [
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
                        'udls' => [
                            'links' => [
                                'self',
                                'related',
                            ],
                            'data' => [
                                0 => [
                                    'type',
                                    'id',
                                ]
                            ],
                        ],
                    ],
                ],
                'included' => [
                    0 => [ // UDLVALUES
                        'type',
                        'id',
                        'attributes' => [
                            'udlId',
                            'udlName',
                            'udlValue',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                    1 => [ // UDLS
                        'type',
                        'id',
                        'attributes' => [
                            'companyId',
                            'name',
                            'inputType',
                            'legacyUdlField'
                        ],
                        'links' => [
                            'self',
                        ],
                    ]
                ]
            ]);
    }

    public function testUpdateCompanyIncludeUpdateUdlsUpdateUdlValue()
    {
        $company = factory(\WA\DataStore\Company\Company::class)->create();

        $udl = factory(\WA\DataStore\Udl\Udl::class)->create(['companyId' => $company->id]);

        $udlvalue1 = factory(\WA\DataStore\UdlValue\UdlValue::class)->create(['udlId' => $udl->id]);

        $udls = DB::table('udls')->where('companyId', $company->id)->get();
        $this->assertCount(1, $udls);
        $this->assertEquals($udls[0]->id, $udl->id);

        $udlvalues = DB::table('udl_values')->where('udlId', $udl->id)->orderBy('id')->get();
        $this->assertCount(1, $udlvalues);
        $this->assertEquals($udlvalues[0]->id, $udlvalue1->id);
        $this->assertEquals($udlvalues[0]->name, $udlvalue1->name);

        // ADD ONE UDL
        $res = $this->json('PATCH', 'companies/'.$company->id.'?include=udls,udls.udlvalues',
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
                    'relationships' => [
                        'udls' => [
                            'data' => [
                                [
                                    'type' => 'udls',
                                    'id'  => $udl->id,
                                    'attributes' => [
                                        'name' => 'Updated Name UDL',
                                        'inputType' => 'string'
                                    ],
                                    'relationships' => [
                                        'udlvalues' => [
                                            'data' => [
                                                ['type' => 'udlvalues', 'id' => $udlvalue1->id, 'name' => 'Updated Name UDLVALUE']
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ])
            //Log::debug('testUpdateCompanyIncludeUpdateUdlsUpdateUdlValue: '.print_r($res->response->getContent(), true));
            ->seeJson(
            [
                'type'              => 'companies',
                'name'              => 'SirionDev',
                'label'             => 'Sirion',
                'active'            => 1,
                'udlpath'           => null,
                'isCensus'          => 0,
                'udlPathRule'       => null,
                'assetPath'         => '/var/www/clean/storage/clients/clients/acme',
                'shortName'         => 'ShortName',
                'currentBillMonth'  => $company->currentBillMonth,
                'defaultLocation'   => $company->defaultLocation
            ])
            ->seeJsonStructure(
            [
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
                        'udls' => [
                            'links' => [
                                'self',
                                'related',
                            ],
                            'data' => [
                                0 => [
                                    'type',
                                    'id',
                                ]
                            ],
                        ],
                    ],
                ],
                'included' => [
                    0 => [ // UDLVALUES
                        'type',
                        'id',
                        'attributes' => [
                            'udlId',
                            'udlName',
                            'udlValue',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                    1 => [ // UDLS
                        'type',
                        'id',
                        'attributes' => [
                            'companyId',
                            'name',
                            'inputType',
                            'legacyUdlField'
                        ],
                        'links' => [
                            'self',
                        ],
                    ]
                ]
            ]);

            $udlsFinal = DB::table('udls')->where('companyId', $company->id)->get();
            $this->assertCount(1, $udls);
            $this->assertEquals($udlsFinal[0]->id, $udl->id);
            $this->assertEquals($udlsFinal[0]->name, 'Updated Name UDL');

            $udlvaluesFinal = DB::table('udl_values')->where('udlId', $udl->id)->orderBy('id')->get();
            $this->assertCount(1, $udlvaluesFinal);
            $this->assertEquals($udlvaluesFinal[0]->id, $udlvalue1->id);
            $this->assertEquals($udlvaluesFinal[0]->name, 'Updated Name UDLVALUE');
    }

    public function testUpdateCompanyIncludeUpdateUdlsUpdateUdlValueNoJsonUdl()
    {
        $company = factory(\WA\DataStore\Company\Company::class)->create();

        $udl = factory(\WA\DataStore\Udl\Udl::class)->create(['companyId' => $company->id]);

        $udlvalue1 = factory(\WA\DataStore\UdlValue\UdlValue::class)->create(['udlId' => $udl->id]);

        $udls = DB::table('udls')->where('companyId', $company->id)->get();
        $this->assertCount(1, $udls);
        $this->assertEquals($udls[0]->id, $udl->id);

        $udlvalues = DB::table('udl_values')->where('udlId', $udl->id)->orderBy('id')->get();
        $this->assertCount(1, $udlvalues);
        $this->assertEquals($udlvalues[0]->id, $udlvalue1->id);
        $this->assertEquals($udlvalues[0]->name, $udlvalue1->name);

        // ADD ONE UDL
        $res = $this->json('PATCH', 'companies/'.$company->id.'?include=udls,udls.udlvalues',
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
                    'relationships' => [
                        'udls' => [
                            'data' => [
                                [
                                    'type' => 'no valid',
                                    'id'  => $udl->id,
                                    'attributes' => [
                                        'name' => 'Updated Name UDL',
                                        'inputType' => 'string'
                                    ],
                                    'relationships' => [
                                        'udlvalues' => [
                                            'data' => [
                                                ['type' => 'udlvalues', 'id' => $udlvalue1->id, 'name' => 'Updated Name UDLVALUE']
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ])
            //Log::debug('testUpdateCompanyIncludeUpdateUdlsUpdateUdlValue: '.print_r($res->response->getContent(), true));
            ->seeJson(
            [
                'udls'      => 'the Udl  has not been updated'
            ])
            ->seeJsonStructure(
            [
                'errors' => [
                    'udls'
                ]
            ]);

            $udlsFinal = DB::table('udls')->where('companyId', $company->id)->get();
            //Log::debug('2531-udlsFinal: '.print_r($udlsFinal, true));
            $this->assertCount(1, $udls);
            $this->assertEquals($udlsFinal[0]->id, $udl->id);
            $this->assertEquals($udlsFinal[0]->name, $udl->name);

            $udlvaluesFinal = DB::table('udl_values')->where('udlId', $udl->id)->orderBy('id')->get();
            //Log::debug('2537-udlvaluesFinal: '.print_r($udlvaluesFinal, true));
            $this->assertCount(1, $udlvaluesFinal);
            $this->assertEquals($udlvaluesFinal[0]->id, $udlvalue1->id);
            $this->assertEquals($udlvaluesFinal[0]->name, $udlvalue1->name);
    }

    public function testUpdateCompanyIncludeUpdateUdlsUpdateUdlValueNoJsonUdlValue()
    {
        $company = factory(\WA\DataStore\Company\Company::class)->create();

        $udl = factory(\WA\DataStore\Udl\Udl::class)->create(['companyId' => $company->id]);

        $udlvalue1 = factory(\WA\DataStore\UdlValue\UdlValue::class)->create(['udlId' => $udl->id]);

        $udls = DB::table('udls')->where('companyId', $company->id)->get();
        $this->assertCount(1, $udls);
        $this->assertEquals($udls[0]->id, $udl->id);

        $udlvalues = DB::table('udl_values')->where('udlId', $udl->id)->orderBy('id')->get();
        $this->assertCount(1, $udlvalues);
        $this->assertEquals($udlvalues[0]->id, $udlvalue1->id);
        $this->assertEquals($udlvalues[0]->name, $udlvalue1->name);

        // ADD ONE UDL
        $res = $this->json('PATCH', 'companies/'.$company->id.'?include=udls,udls.udlvalues',
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
                    'relationships' => [
                        'udls' => [
                            'data' => [
                                [
                                    'type' => 'udls',
                                    'id'  => $udl->id,
                                    'attributes' => [
                                        'name' => 'Updated Name UDL',
                                        'inputType' => 'string'
                                    ],
                                    'relationships' => [
                                        'no valid' => [
                                            'data' => [
                                                ['type' => 'udlvalues', 'id' => $udlvalue1->id, 'name' => 'Updated Name UDLVALUE']
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ])
            //Log::debug('testUpdateCompanyIncludeUpdateUdlsUpdateUdlValue: '.print_r($res->response->getContent(), true));
            ->seeJsonStructure(
            [
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
                        'shortName',
                        'currentBillMonth',
                        'defaultLocation'
                    ],
                    'links' => [
                        'self',
                    ],
                    'relationships' => [
                        'udls' => [
                            'links' => [
                                'self',
                                'related',
                            ],
                            'data' => [
                                0 => [
                                    'type',
                                    'id',
                                ]
                            ],
                        ],
                    ],
                ],
                'included' => [
                    0 => [ // UDLVALUES
                        'type',
                        'id',
                        'attributes' => [
                            'udlId',
                            'udlName',
                            'udlValue',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                    1 => [ // UDLS
                        'type',
                        'id',
                        'attributes' => [
                            'companyId',
                            'name',
                            'inputType',
                            'legacyUdlField'
                        ],
                        'links' => [
                            'self',
                        ],
                        'relationships' => [
                            'udlvalues' => [
                                'data' => [
                                    [
                                        'type',
                                        'id'
                                    ]                                    
                                ],
                                'links' => [
                                    'self',
                                    'related'
                                ]
                            ]
                        ]
                    ]
                ]
            ]);

            $udlsFinal = DB::table('udls')->where('companyId', $company->id)->get();
            $this->assertCount(1, $udls);
            $this->assertEquals($udlsFinal[0]->id, $udl->id);
            $this->assertEquals($udlsFinal[0]->name, 'Updated Name UDL');

            $udlvaluesFinal = DB::table('udl_values')->where('udlId', $udl->id)->orderBy('id')->get();
            $this->assertCount(1, $udlvaluesFinal);
            $this->assertEquals($udlvaluesFinal[0]->id, $udlvalue1->id);
            $this->assertEquals($udlvaluesFinal[0]->name, $udlvalue1->name);
    }

    public function testUpdateCompanyIncludeUpdateUdlsUpdateUdlValueNoJsonUdlValueType()
    {
        $company = factory(\WA\DataStore\Company\Company::class)->create();

        $udl = factory(\WA\DataStore\Udl\Udl::class)->create(['companyId' => $company->id]);

        $udlvalue = factory(\WA\DataStore\UdlValue\UdlValue::class)->create(['udlId' => $udl->id]);

        $udls = DB::table('udls')->where('companyId', $company->id)->get();
        $this->assertCount(1, $udls);
        $this->assertEquals($udls[0]->id, $udl->id);

        $udlvalues = DB::table('udl_values')->where('udlId', $udl->id)->orderBy('id')->get();
        $this->assertCount(1, $udlvalues);
        $this->assertEquals($udlvalues[0]->id, $udlvalue->id);
        $this->assertEquals($udlvalues[0]->name, $udlvalue->name);

        // ADD ONE UDL
        $res = $this->json('PATCH', 'companies/'.$company->id.'?include=udls,udls.udlvalues',
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
                    'relationships' => [
                        'udls' => [
                            'data' => [
                                [
                                    'type' => 'udls',
                                    'id'  => $udl->id,
                                    'attributes' => [
                                        'name' => 'Updated Name UDL',
                                        'inputType' => 'string'
                                    ],
                                    'relationships' => [
                                        'udlvalues' => [
                                            'data' => [
                                                ['type' => 'no valid', 'id' => $udlvalue->id, 'name' => 'Updated Name UDLVALUE']
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ])
            //Log::debug('testUpdateCompanyIncludeUpdateUdlsUpdateUdlValueNoJsonUdlValueType: '.print_r($res->response->getContent(), true));
            ->seeJson(
            [
                'udls'      => 'the Udl  has not been updated'
            ])
            ->seeJsonStructure(
            [
                'errors' => [
                    'udls'
                ]
            ]);

            $udlsFinal = DB::table('udls')->where('companyId', $company->id)->get();
            //Log::debug('udlsFinal: '.print_r($udlsFinal, true));
            $this->assertCount(1, $udlsFinal);
            $this->assertEquals($udlsFinal[0]->id, $udl->id);
            $this->assertEquals($udlsFinal[0]->name, $udl->name);

            $udlvaluesFinal = DB::table('udl_values')->where('udlId', $udl->id)->orderBy('id')->get();
            //Log::debug('udlvaluesFinal: '.print_r($udlvaluesFinal, true));
            $this->assertCount(1, $udlvaluesFinal);
            $this->assertEquals($udlvaluesFinal[0]->id, $udlvalue->id);
            $this->assertEquals($udlvaluesFinal[0]->name, $udlvalue->name);
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
        $responseDel = $this->call('DELETE', 'companies/10');
        $this->assertEquals(404, $responseDel->status());
    }

    public function testCreateImportUserJobWithWrongRequest()
    {
        // check company is exist
        $companyId = 'random';
        $uri = "companies/$companyId/jobs";
        $response = $this->call('POST', $uri);
        $this->assertEquals(404, $response->status());

        // check attach file
        $company = factory(\WA\DataStore\Company\Company::class)->create(['name' => 'random-company']);
        $companyId = $company->id;
        $uri = "companies/$companyId/jobs";
        $response = $this->call('POST', $uri);
        $this->assertEquals(400, $response->status());

        // check attach file is csv
        $targetPath = "./storage/clients/{$company->name}";
        $targetFile = "$targetPath/filename.png";
        if(!is_dir($targetPath)){
            mkdir($targetPath, 0755, true);
        }
        copy('./database/seeds/imagesseeder/phpFmndT1.png', $targetFile);

        $uploadedFile = new Symfony\Component\HttpFoundation\File\UploadedFile(
            $targetFile,
            'iphone6.png',
            'image/png',
            235417,
            null,
            true
        );
        $response = $this->call('POST', $uri, [], [], ['csv' => $uploadedFile]);
        $this->assertEquals(400, $response->status());

        @unlink($targetFile);
    }

    /**
     *
     * @group CompaniesControllerTest
     */
    public function testCompanyCreateJobSuccessfully()
    {
        $company = factory(\WA\DataStore\Company\Company::class)->create(['name' => 'random-company']);
        
        $udl1 = factory(\WA\DataStore\Udl\Udl::class)->create([
            'companyId'         => $company->id,
            'name'              => 'udl-for-tests',
            'legacyUdlField'    => null,
            'inputType'         => 'string'
        ]);

        $udl1Value1 = factory(\WA\DataStore\UdlValue\UdlValue::class)->create([
            'udlId' => $udl1->id,
            'name' => 'udl-value-1'
        ]);
        $udl1Value2 = factory(\WA\DataStore\UdlValue\UdlValue::class)->create([
            'udlId' => $udl1->id,
            'name' => 'udl-value-2'
        ]);
        $udl1Value3 = factory(\WA\DataStore\UdlValue\UdlValue::class)->create([
            'udlId' => $udl1->id,
            'name' => 'udl-value-3'
        ]);
    
        $uploadedFile = new Symfony\Component\HttpFoundation\File\UploadedFile(
            './database/seeds/import/employee--test.csv',
            'employee--test.csv',
            null,null,null,true
        );

        $response = $this->call('POST', "companies/{$company->id}/jobs", ['test' => '1'], [], ['csv' => $uploadedFile]);
        $responseContents = json_decode($response->getContent(), true);

        //\Log::debug('JSON FOR GET OF CompanyUsersImportJob:');
        //\Log::debug(json_encode($responseContents, JSON_PRETTY_PRINT));

        $this->seeJsonStructure([
            'data' => [
                'type',
                'id',
                'attributes' => [
                    'jobType',
                    'companyId',
                    'path',
                    'file',
                    'totalUsers',
                    'createdUsers',
                    'creatableUsers',
                    'updatedUsers',
                    'updatableUsers',
                    'failedUsers',
                    'CSVfields',
                    'DBfields',
                    'sampleUser' => [
                        /*
                        'email',
                        'alternateEmail',
                        'password',
                        'username',
                        'confirmation_code',
                        'confirmed',
                        'firstName',
                        'lastName',
                        'alternateFirstName',
                        'supervisorEmail',
                        'companyUserIdentifier',
                        'isSupervisor',
                        'isValidator',
                        'isActive',
                        'defaultLang',
                        'level',
                        'notify',
                        'companyId',
                        'supervisorId',
                        'externalId',
                        'approverId',
                        'defaultLocationId',
                        //*/
                    ],
                    'mappings' => [],
                    'status',
                    // 'errors',
                    'created_by_id',
                    'updated_by_id',
                    'created_at' => [
                        'date',
                        'timezone_type',
                        'timezone',
                    ],
                    'updated_at' => [
                        'date',
                        'timezone_type',
                        'timezone',
                    ]
                ],
                'links' => [
                    'self',
                ]
            ]
        ]);

        $fieldsInCSVFields = array_diff([
                'email',
                'alternateEmail',
                'password',
                'username',
                'confirmation_code',
                'confirmed',
                'firstName',
                'lastName',
                'alternateFirstName',
                'supervisorEmail',
                'companyUserIdentifier',
                'isSupervisor',
                'isValidator',
                'isActive',
                'defaultLang',
                'level',
                'notify',
                'companyId',
                'supervisorId',
                'externalId',
                'approverId',
                'defaultLocationId',
                'udl-for-tests',
            ],
            $responseContents['data']['attributes']['CSVfields']
        );

        $fieldsInDBFields = array_diff([
                'uuid',
                'identification',
                'email',
                'alternateEmail',
                'username',
                'firstName',
                'lastName',
                'alternateFirstName',
                'isSupervisor',
                'isValidator',
                'isActive',
                'hierarchy',
                'defaultLang',
                'notes',
                'level',
                // And the UDLs already injected from the factories and then, appearing in the CSV:
                'udl-for-tests'
            ],
            $responseContents['data']['attributes']['DBfields']
        );

        $this->assertEmpty($fieldsInCSVFields);
        $this->assertEmpty($fieldsInDBFields);

        // // \Log::debug(json_encode($fieldsInCSVFields, JSON_PRETTY_PRINT));
        // // \Log::debug(json_encode($fieldsInDBFields, JSON_PRETTY_PRINT));
        return $responseContents['data']['id'];

    }

    /**
     *
     * @group CompaniesControllerTest
     */
    public function testCompanyGetJob()
    {
        $job = factory(\WA\DataStore\Company\CompanyUserImportJob::class)->create();

        $response = $this->json('GET', "companies/{$job->companyId}/jobs/{$job->id}");
        //\Log::debug(json_encode(json_decode($response->response->getContent()), JSON_PRETTY_PRINT));
        $response->seeJsonStructure([
            'data' => [
                'id',
                'type',
                'attributes' => [
                    'jobType',
                    'status',
                    'companyId',
                    'path',
                    'file',
                    'totalUsers',
                    'createdUsers',
                    'creatableUsers',
                    'updatedUsers',
                    'updatableUsers',
                    'sampleUser',
                    'CSVfields',
                    'DBfields',
                    'mappings'
                ]
            ]
        ]);
    }

    /**
     *
     * @group CompaniesControllerTest
     */
    public function testBORRAR()
    {
        $job = factory(\WA\DataStore\Company\CompanyUserImportJob::class)->create(['companyId' => 1]);

        $response = $this->json('GET', 'companies/1');
        //\Log::debug(json_encode(json_decode($response->response->getContent()), JSON_PRETTY_PRINT));
        $response->seeJsonStructure([
                'data' => [
                    'id',
                    'type',
                    'attributes' => [

                        
                    ]
                ]
            ]);
    }

    /**
     *
     * @group CompaniesControllerTest
     */
    public function testCompanyPatchJob()
    {

        $job = factory(\WA\DataStore\Company\CompanyUserImportJob::class)->create([
            'filepath' => base_path() . '/storage/clients/random-company/employee.csv',
            'companyId' => 1
        ]);
        $this->withoutJobs();
        $response = $this->call('PATCH', "companies/1/jobs/{$job->id}",
            [
                'data' => [
                    'id' => $job->id,
                    'type' => 'jobs',
                    'attributes' => [
                        'status' => 'Pending',
                        'totalUsers' => $job->total,
                        'createdUsers' => 0,
                        'updatedUsers' => 0,
                        'errors' => 0,
                        'sampleUser' => [
                            'email' => 'douglas.rolfson@example.org1',
                            'alternateEmail' => 'pagac.ashlee@example.org',
                            'password' => 'user',
                            'username' => 'douglas.rolfson',
                            'confirmation_code' => 'b95c05f09018e7d91c5a67c8d66b68f4',
                            'confirmed' => '1',
                            'firstName' => 'Britney',
                            'lastName' => 'Prosacco',
                            'alternateFirstName' => 'Larissa',
                            'supervisorEmail' => 'leon62@example.org',
                            'companyUserIdentifier' => '2',
                            'isSupervisor' => '0',
                            'isValidator' => '0',
                            'isActive' => '1',
                            'defaultLang' => 'en',
                            'level' => '0',
                            'notify' => '0',
                            'companyId' => '3',
                            'supervisorId' => '3',
                            'externalId' => '',
                            'approverId' => '1',
                            'defaultLocationId' => '52'
                        ],
                        'CSVfields' => [
                            'email',
                            'alternateEmail',
                            'password',
                            'username',
                            'confirmation_code',
                            'confirmed',
                            'firstName',
                            'lastName',
                            'alternateFirstName',
                            'supervisorEmail',
                            'companyUserIdentifier',
                            'isSupervisor',
                            'isValidator',
                            'isActive',
                            'defaultLang',
                            'level',
                            'notify',
                            'companyId',
                            'supervisorId',
                            'externalId',
                            'approverId',
                            'defaultLocationId'
                        ],
                        'DBfields' => [
                            'email',
                            'alternateEmail',
                            'password',
                            'username',
                            'confirmation_code',
                            'confirmed',
                            'firstName',
                            'lastName',
                            'alternateFirstName',
                            'supervisorEmail',
                            'companyUserIdentifier',
                            'isSupervisor',
                            'isValidator',
                            'isActive',
                            'defaultLang',
                            'notes',
                            'level',
                            'notify',
                            'Cost Center',
                            'Position',
                            'Sector',
                            'Vehicle'
                        ],
                        'mappings' => [
                            ['csvField' =>'email','dbField' =>'email'],
                            ['csvField' =>'companyId','dbField' =>'companyId']
                        ]
                    ]
                ]
            ]);
        //\Log::debug($response->getcontent());
        $this->assertEquals(200, $response->getStatusCode());
        $this->seeJsonStructure([
            'data' => [
                'id',
                'type',
                'attributes' => [
                    'status',
                    'totalUsers',
                    'createdUsers',
                    'updatedUsers',
                    // 'errors',
                    'sampleUser',
                    'CSVfields',
                    'DBfields',
                    'mappings'
                ]
            ]
        ]);
    }


    /**
     * 
     * @group CompaniesControllerTest
     */
    public function testCompanyUsersImportationProcessAll_2 () {

        $company = factory(\WA\DataStore\Company\Company::class)->create([
            'name' => 'random-company'
        ]);

        $udl1 = factory(\WA\DataStore\Udl\Udl::class)->create([
            'companyId'         => $company->id,
            'name'              => 'udl-for-tests',
            'legacyUdlField'    => null,
            'inputType'         => 'string'
        ]);

        $udl1Value1 = factory(\WA\DataStore\UdlValue\UdlValue::class)->create([
            'udlId' => $udl1->id,
            'name' => 'udl-value-1'
        ]);
        $udl1Value2 = factory(\WA\DataStore\UdlValue\UdlValue::class)->create([
            'udlId' => $udl1->id,
            'name' => 'udl-value-2'
        ]);
        $udl1Value3 = factory(\WA\DataStore\UdlValue\UdlValue::class)->create([
            'udlId' => $udl1->id,
            'name' => 'udl-value-3'
        ]);
    
        $uploadedFile = new Symfony\Component\HttpFoundation\File\UploadedFile(
            './database/seeds/import/employee--test.csv',
            'employee--test.csv',
            null,null,null,true
        );

        $usersCount1 = User::where('companyId', $company->id)->count();
        $jobsCount1 = CompanyUserImportJob::where('companyId', $company->id)->count();
        //\Log::debug('testCompanyUsersImportationProcessAll_2 - usersCount1: '.print_r($usersCount1, true));
        $this->assertEquals(0, $jobsCount1);
        $this->assertEquals(0, $usersCount1);

        $postUsers = $this->call('POST', "companies/{$company->id}/jobs/", 
            ['test' => 1],
            [],
            ['csv' => $uploadedFile]
        );

        $job = json_decode($postUsers->getContent())->data;

        $usersCount2 = User::where('companyId', $company->id)->count();
        $jobsCount2 = CompanyUserImportJob::where('companyId', $company->id)->count();

        // There is 1 user because if there is none, the controller creates one.
        $this->assertEquals(1, $usersCount2);
        $this->assertEquals(1, $jobsCount2);

        $patchJob = $this->call('PATCH', "companies/{$company->id}/jobs/{$job->id}", [
            'data' => [
                'type' => 'jobs',
                'id' => $job->id,
                'attributes' => [
                    'companyId' => '1',
                    'filepath' => '/var/www/html/wirelessanalytics/clean-api/storage/clients/ward-steuber-and-mayert/employee1497367323.csv',
                    'filename' => 'employee1497367323.csv',
                    'totalUsers' => 0,
                    'createdUsers' => 0,
                    'updatedUsers' => 0,
                    'failedUsers' => 0,
                    'CSVfields' => [
                        'email',
                        'alternateEmail',
                        'password',
                        'username',
                        'confirmation_code',
                        'confirmed',
                        'firstName',
                        'lastName',
                        'alternateFirstName',
                        'supervisorEmail',
                        'companyUserIdentifier',
                        'isSupervisor',
                        'isValidator',
                        'isActive',
                        'defaultLang',
                        'level',
                        'notify',
                        'companyId',
                        'supervisorId',
                        'externalId',
                        'approverId',
                        'defaultLocationId'
                    ],
                    'DBfields' => [
                        'uuid',
                        'identification',
                        'email',
                        'alternateEmail',
                        'username',
                        'firstName',
                        'lastName',
                        'alternateFirstName',
                        'isSupervisor',
                        'isValidator',
                        'isActive',
                        'hierarchy',
                        'defaultLang',
                        'notes',
                        'level',
                        'Cost Center',
                        'Division',
                        'Position'
                    ],
                    'sampleUser' => [
                        'email' => 'douglas.rolfson@example.org',
                        'alternateEmail' => 'pagac.ashlee@example.org',
                        'password' => 'user',
                        'username' => 'douglas.rolfson',
                        'confirmation_code' => 'b95c05f09018e7d91c5a67c8d66b68f4',
                        'confirmed' => '1',
                        'firstName' => 'Britney',
                        'lastName' => 'Prosacco',
                        'alternateFirstName' => 'Larissa',
                        'supervisorEmail' => 'leon62@example.org',
                        'companyUserIdentifier' => '2',
                        'isSupervisor' => '0',
                        'isValidator' => '0',
                        'isActive' => '1',
                        'defaultLang' => 'en',
                        'level' => '0',
                        'notify' => '0',
                        'companyId' => '3',
                        'supervisorId' => '3',
                        'externalId' => '\\N',
                        'approverId' => '1',
                        'defaultLocationId' => '52'
                    ],
                    'mappings' => [
                        ['csvField' =>'email','dbField' =>'email'],
                        ['csvField' =>'companyId','dbField' =>'companyId']
                    ],
                    'status' => 'Pending',
                    'errors' => [],
                    'created_by_id' => 1,
                    'updated_by_id' => 1,
                    'created_at' => [
                        'date' => '2017-06-13 15:22:03.000000',
                        'timezone_type' => 3,
                        'timezone' => 'UTC'
                    ],
                    'updated_at' => [
                        'date' => '2017-06-13 15:22:03.000000',
                        'timezone_type' => 3,
                        'timezone' => 'UTC'
                    ]
                ],
                'links' => [
                    'self' => 'clean.api/companyuserimportjobs/5'
                ]
            ]
        ]);

        $usersCount3 = User::where('companyId', $company->id)->get();

        $this->assertEquals(200, $patchJob->getStatusCode());

    }

}
