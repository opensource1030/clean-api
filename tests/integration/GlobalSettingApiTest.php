<?php

use Laravel\Lumen\Testing\DatabaseMigrations;

class GlobalSettingApiTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * A basic functional test for GlobalSetting.
     */
    public function testGetGlobalSetting()
    {
        factory(\WA\DataStore\GlobalSetting\GlobalSetting::class, 40)->create();

        $res = $this->json('GET', 'globalsettings');
        $res->seeJsonStructure([
                'data' => [
                    0 => [
                        'type',
                        'id',
                        'attributes' => [
                            'name',
                            'label',
                            'description',
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

    public function testGetGlobalSettingById()
    {
        $globalSetting = factory(\WA\DataStore\GlobalSetting\GlobalSetting::class)->create();

        $res = $this->json('GET', 'globalsettings/'.$globalSetting->id);
        $res->seeJson([
                'type' => 'globalsettings',
                'label' => $globalSetting->label,
                'name' => $globalSetting->name,
                'description' => $globalSetting->description,
            ]);
    }

    public function testGetGlobalSettingByIdIncludeValues()
    {
        $globalSetting = factory(\WA\DataStore\GlobalSetting\GlobalSetting::class)->create();

        $globalSettingValue1 = factory(\WA\DataStore\GlobalSettingValue\GlobalSettingValue::class)->create([
            'globalSettingId' => $globalSetting->id
        ]);

        $globalSettingValue2 = factory(\WA\DataStore\GlobalSettingValue\GlobalSettingValue::class)->create([
            'globalSettingId' => $globalSetting->id
        ]);

        $res = $this->json('GET', 'globalsettings/'.$globalSetting->id.'?include=globalsettingvalues');
        //\Log::debug($res->response->getContent());
        $res->seeJsonStructure([
            'data' => [
                'type',
                'id',
                'attributes' => [
                    'name',
                    'label',
                    'description'
                ],
                'links' => [
                    'self',
                ],
                'relationships' => [
                    'globalsettingvalues' => [
                        'links' => [
                            'self',
                            'related'
                        ],
                        'data' => [
                            0 => [
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
                        'label',
                        'globalSettingId'
                    ],
                    'links' => [
                        'self'
                    ]
                ]
            ]
        ]);
    }

    public function testCreateGlobalSetting()
    {
        $company = factory(\WA\DataStore\Company\Company::class)->create();

        $this->json('POST', 'globalsettings',
            [
                'data' => [
                    'type' => 'globalsettings',
                    'attributes' => [
                        'label' => "Example Test",
                        'name' => "Example Test",
                        'description' => "Description Test"
                    ],
                ],
            ])
            ->seeJson([
                'type' => 'globalsettings',
                'label' => "Example Test",
                'name' => "Example Test",
                'description' => "Description Test"
            ]);
    }

    public function testUpdateGlobalSetting()
    {
        $company1 = factory(\WA\DataStore\GlobalSetting\GlobalSetting::class)->create();
        $company2 = factory(\WA\DataStore\GlobalSetting\GlobalSetting::class)->create();

        $globalSetting1 = factory(\WA\DataStore\GlobalSetting\GlobalSetting::class)->create([
            'name' => "Example Test",
            'label' => "Example Test",
            'description' => "Description Test",
        ]);

        $globalSetting2 = factory(\WA\DataStore\GlobalSetting\GlobalSetting::class)->create([
            'name' => "Other Example Test",
            'label' => "Other Example Test",
            'description' => "Other Description Test",
        ]);

        $this->assertNotEquals($globalSetting1->name, $globalSetting2->name);
        $this->assertNotEquals($globalSetting1->description, $globalSetting2->description);
        $this->assertNotEquals($globalSetting1->label, $globalSetting2->label);

        $this->json('GET', 'globalsettings/'.$globalSetting1->id)
            ->seeJson([
                'type' => 'globalsettings',
                'label' => $globalSetting1->label,
                'name' => $globalSetting1->name,
                'description' => $globalSetting1->description,
            ]);

        $this->json('PATCH', 'globalsettings/'.$globalSetting1->id,
            [
                'data' => [
                    'type' => 'globalsettings',
                    'attributes' => [
                        'label' => $globalSetting2->label,
                        'name' => $globalSetting2->name,
                        'description' => $globalSetting2->description,
                    ],
                ],
            ])
            ->seeJson([
                //'type' => 'address',
                'id' => $globalSetting1->id,
                'label' => $globalSetting2->label,
                'name' => $globalSetting2->name,
                'description' => $globalSetting2->description,
            ]);
    }

    public function testDeleteGlobalSettingIfExists()
    {
        $globalSetting = factory(\WA\DataStore\GlobalSetting\GlobalSetting::class)->create();
        $responseDel = $this->call('DELETE', 'globalsettings/'.$globalSetting->id);
        $this->assertEquals(200, $responseDel->status());
        $responseGet = $this->call('GET', 'globalsettings/'.$globalSetting->id);
        $this->assertEquals(404, $responseGet->status());
    }

    public function testDeleteGlobalSettingIfNoExists()
    {
        $responseDel = $this->call('DELETE', 'globalsettings/1');
        $this->assertEquals(404, $responseDel->status());
    }
}
