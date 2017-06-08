<?php

use Laravel\Lumen\Testing\DatabaseMigrations;

class CompanySettingApiTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * A basic functional test for Address.
     */
    public function testGetCompanySetting()
    {
        factory(\WA\DataStore\Company\CompanySetting::class, 40)->create();

        $res = $this->json('GET', 'companysettings');
        $res->seeJsonStructure([
                'data' => [
                    0 => [
                        'type',
                        'id',
                        'attributes' => [
                            'value',
                            'name',
                            'description',
                            'companyId',
                            'created_at' => [
                                'date',
                                'timezone_type',
                                'timezone',
                            ],
                            'updated_at' => [
                                'date',
                                'timezone_type',
                                'timezone',
                            ],
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

    public function testGetCompanySettingById()
    {
        $companySetting = factory(\WA\DataStore\Company\CompanySetting::class)->create();

        $res = $this->json('GET', 'companysettings/'.$companySetting->id)
            ->seeJson([
                'type' => 'companysettings',
                'value' => $companySetting->value,
                'name' => $companySetting->name,
                'description' => $companySetting->description,
                'companyId' => $companySetting->companyId
            ]);
    }

    public function testCreateCompanySetting()
    {
        $company = factory(\WA\DataStore\Company\Company::class)->create();

        $this->json('POST', 'companysettings',
            [
                'data' => [
                    'type' => 'companysettings',
                    'attributes' => [
                        'value' => "enable",
                        'name' => "Example Test",
                        'description' => "Description Test",
                        'companyId' => $company->id
                    ],
                ],
            ])
            ->seeJson([
                'type' => 'companysettings',
                'value' => "enable",
                'name' => "Example Test",
                'description' => "Description Test",
                'companyId' => $company->id
            ]);
    }

    public function testUpdateCompanySetting()
    {
        $company1 = factory(\WA\DataStore\Company\Company::class)->create();
        $company2 = factory(\WA\DataStore\Company\Company::class)->create();

        $companySetting1 = factory(\WA\DataStore\Company\CompanySetting::class)->create([
            'value' => "enable",
            'name' => "Example Test",
            'description' => "Description Test",
            'companyId' => $company1->id
        ]);

        $companySetting2 = factory(\WA\DataStore\Company\CompanySetting::class)->create([
            'value' => "disable",
            'name' => "Other Example Test",
            'description' => "Other Description Test",
            'companyId' => $company2->id
        ]);

        $this->assertNotEquals($companySetting1->value, $companySetting2->value);
        $this->assertNotEquals($companySetting1->name, $companySetting2->name);
        $this->assertNotEquals($companySetting1->description, $companySetting2->description);
        $this->assertNotEquals($companySetting1->companyId, $companySetting2->companyId);

        $this->json('GET', 'companysettings/'.$companySetting1->id)
            ->seeJson([
                'type' => 'companysettings',
                'value' => $companySetting1->value,
                'name' => $companySetting1->name,
                'description' => $companySetting1->description,
                'companyId' => $companySetting1->companyId
            ]);

        $this->json('PATCH', 'companysettings/'.$companySetting1->id,
            [
                'data' => [
                    'type' => 'companysettings',
                    'attributes' => [
                        'value' => $companySetting2->value,
                        'name' => $companySetting2->name,
                        'description' => $companySetting2->description,
                        'companyId' => $companySetting2->companyId
                    ],
                ],
            ])
            ->seeJson([
                //'type' => 'address',
                'id' => $companySetting1->id,
                'value' => $companySetting2->value,
                'name' => $companySetting2->name,
                'description' => $companySetting2->description,
                'companyId' => $companySetting2->companyId
            ]);
    }

    public function testDeleteCompanySettingIfExists()
    {
        $companySetting = factory(\WA\DataStore\Company\CompanySetting::class)->create();
        $responseDel = $this->call('DELETE', 'companysettings/'.$companySetting->id);
        $this->assertEquals(200, $responseDel->status());
        $responseGet = $this->call('GET', 'companysettings/'.$companySetting->id);
        $this->assertEquals(404, $responseGet->status());
    }

    public function testDeleteCompanySettingIfNoExists()
    {
        $responseDel = $this->call('DELETE', 'companysettings/1');
        $this->assertEquals(404, $responseDel->status());
    }
}
