<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use WA\DataStore\User\User;

class UsersApiTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * A basic functional test for user endpoints.
     */

    public function testGetUsers()
    {
        $user = factory(\WA\DataStore\User\User::class, 20)->create();

        $res = $this->json('GET', '/users')
        //Log::debug("Users: ".print_r($res->response->getContent(), true));
            ->seeJsonStructure([
                'data' => [
                    0 => [
                        'type', 
                        'id',
                        'attributes' => [
                            'uuid',
                            'identification',
                            'email',
                            'alternateEmail',
                            'password',
                            'username',
                            'confirmation_code',
                            'remember_token',
                            'confirmed',
                            'firstName',
                            'lastName',
                            'alternateFirstName',
                            'supervisorEmail',
                            'companyUserIdentifier',
                            'isSupervisor',
                            'isValidator',
                            'isActive',
                            'rgt',
                            'lft',
                            'hierarchy',
                            'defaultLang',
                            'notes',
                            'level',
                            'notify',
                            'companyId',
                            'syncId',
                            'supervisorId',
                            'externalId',
                            'approverId',
                            'defaultLocationId',
                            'addressId'
                        ],
                        'links' => [
                            'self',
                        ],
                        'relationships' =>  [
                            'address' => [
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
                ],
                'included' => [
                    0 => [
                        'type',
                        'id',
                        'attributes' => [
                            'address',
                            'city',
                            'state',
                            'country',
                            'postalCode'
                        ],
                        'links' => [
                            'self'
                        ]
                    ]
                    
                ],
                'meta' => [
                    'sort',
                    'filter',
                    'fields',
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
                    'first',
                    'next',
                    'last',
                ],
            ]);
    }

    public function testGetUserByIdIfExists()
    {
        $user = factory(\WA\DataStore\User\User::class)->create();

        $res = $this->get('/users/'.$user->id)
        //Log::debug("Users/id: ".print_r($res->response->getContent(), true));
            ->seeJson([
                    'type' => 'users',
                    'uuid' => $user->uuid,
                    'identification' => $user->identification,
                    'email' => $user->email,
                    'alternateEmail' => $user->alternateEmail,
                    'password' => $user->password,
                    'username' => $user->username,
                    'confirmation_code' => $user->confirmation_code,
                    'remember_token' => $user->remember_token,
                    'confirmed' => "$user->confirmed",
                    'firstName' => $user->firstName,
                    'lastName' => $user->lastName,
                    'alternateFirstName' => $user->alternateFirstName,
                    'supervisorEmail' => $user->supervisorEmail,
                    'companyUserIdentifier' => "$user->companyUserIdentifier",
                    'isSupervisor' => "$user->isSupervisor",
                    'isValidator' => "$user->isValidator",
                    'isActive' => "$user->isActive",
                    'rgt' => $user->rgt,
                    'lft' => $user->lft,
                    'hierarchy' => $user->hierarchy,
                    'defaultLang' => $user->defaultLang,
                    'notes' => $user->notes,
                    'level' => "$user->level",
                    'notify' => "$user->notify",
                    'companyId' => "$user->companyId",
                    'syncId' => $user->syncId,
                    'supervisorId' => "$user->supervisorId",
                    'externalId' => $user->externalId,
                    'approverId' => "$user->approverId",
                    'defaultLocationId' => "$user->defaultLocationId",
                    'addressId' => "$user->addressId"
                ])
            ->seeJsonStructure([
                'data' => [
                    'type', 
                    'id',
                    'attributes' => [
                        'uuid',
                        'identification',
                        'email',
                        'alternateEmail',
                        'password',
                        'username',
                        'confirmation_code',
                        'remember_token',
                        'confirmed',
                        'firstName',
                        'lastName',
                        'alternateFirstName',
                        'supervisorEmail',
                        'companyUserIdentifier',
                        'isSupervisor',
                        'isValidator',
                        'isActive',
                        'rgt',
                        'lft',
                        'hierarchy',
                        'defaultLang',
                        'notes',
                        'level',
                        'notify',
                        'companyId',
                        'syncId',
                        'supervisorId',
                        'externalId',
                        'approverId',
                        'defaultLocationId',
                        'addressId'
                    ],
                    'links' => [
                        'self',
                    ],
                    'relationships' =>  [
                        'address' => [
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
                            'address',
                            'city',
                            'state',
                            'country',
                            'postalCode',
                            'created_at',
                            'updated_at'
                        ],
                        'links' => [
                            'self'
                        ]
                    ]
                ],
                'meta' => [
                    'sort',
                    'filter',
                    'fields'
                ]
            ]);
    }

    public function testGetUserByIdIfNoExists()
    {
        $userId = factory(\WA\DataStore\User\User::class)->create()->id;
        $userId = $userId + 10;

        $response = $this->call('GET', '/users/'.$userId);
        $this->assertEquals(404, $response->status());
    }

    public function testGetLoggedInUser()
    {
        /*$this->markTestSkipped(
              '.'
            );*/
        $grantType = 'password';
        $password = 'user';

        $user = factory(\WA\DataStore\User\User::class)->create([
            'email' => 'email@email.com',
            'password' => '$2y$10$oc9QZeaYYAd.8BPGmXGaFu9cAycKTcBu7LRzmT2J231F0BzKwpxj6'
        ]);

        $scope = factory(\WA\DataStore\Scope\Scope::class)->create(['name' => 'get', 'display_name'=>'get']);
        $scp = $scope->name;
        $oauth = factory(\WA\DataStore\Oauth\Oauth::class)->create([
            'user_Id' => null,
            'name' => 'Password Grant Client',
            'secret' => 'ab9QdKGBXZmZn50aPlf4bLlJtC4BJJNC0M99i7B7',
            'redirect' => 'http://localhost',
            'personal_access_client' => 0,
            'password_client' => 1,
            'revoked' => 0,
        ]);

        $body = [
            'grant_type' => $grantType,
            'username' => $user->email,
            'password' => $password,
            'client_id' => $oauth->id,
            'client_secret' => $oauth->secret,
            'scope'=> $scp
        ];

        $call = $this->call('POST', 'oauth/token', $body, [], [], [], true );
        $array = (array)json_decode($call->getContent());

        $bearerToken = $array['token_type'].' '.$array['access_token'];
        $this->be($user);

        $res = $this->call('GET', 'users/me', [], [], [], ['Accept' => 'application/vnd.v1+json', 'Authorization' => $bearerToken], true );
        $resArray = (array)json_decode($res->getContent());

        $this->assertEquals($resArray['identification'], $user->identification);
        $this->assertEquals($resArray['email'], $user->email);
        $this->assertEquals($resArray['alternateEmail'], $user->alternateEmail);
        $this->assertEquals($resArray['username'], $user->username);
        $this->assertEquals($resArray['firstName'], $user->firstName);
        $this->assertEquals($resArray['lastName'], $user->lastName);
        $this->assertEquals($resArray['alternateFirstName'], $user->alternateFirstName);
        $this->assertEquals($resArray['supervisorEmail'], $user->supervisorEmail);
        $this->assertEquals($resArray['companyUserIdentifier'], $user->companyUserIdentifier);
        $this->assertEquals($resArray['isSupervisor'], $user->isSupervisor);
        $this->assertEquals($resArray['isValidator'], $user->isValidator);
        $this->assertEquals($resArray['rgt'], $user->rgt);
        $this->assertEquals($resArray['lft'], $user->lft);
        $this->assertEquals($resArray['hierarchy'], $user->hierarchy);
        $this->assertEquals($resArray['defaultLang'], $user->defaultLang);
        $this->assertEquals($resArray['notes'], $user->notes);
        $this->assertEquals($resArray['level'], $user->level);
        $this->assertEquals($resArray['notify'], $user->notify);
        $this->assertEquals($resArray['companyId'], $user->companyId);
        $this->assertEquals($resArray['syncId'], $user->syncId);
        $this->assertEquals($resArray['supervisorId'], $user->supervisorId);
        $this->assertEquals($resArray['approverId'], $user->approverId);
        $this->assertEquals($resArray['defaultLocationId'], $user->defaultLocationId);
        $this->assertEquals($resArray['addressId'], $user->addressId);

    }

    /*public function testGetUserByIdandIncludesAssets()
    {
        $user = factory(\WA\DataStore\User\User::class)->create();

        $asset1 = factory(\WA\DataStore\Asset\Asset::class)->create()->id;
        $asset2 = factory(\WA\DataStore\Asset\Asset::class)->create()->id;

        $dataAssets = array($asset1, $asset2);

        $user->assets()->sync($dataAssets);

        $response = $this->json('GET', 'users/'.$user->id.'?include=assets')
            ->seeJsonStructure([
                'data' => [
                    'type',
                    'id',
                    'attributes' => [
                        'uuid',
                        'identification',
                        'email',
                        'alternateEmail',
                        'password',
                        'username',
                        'confirmation_code',
                        'remember_token',
                        'confirmed',
                        'firstName',
                        'lastName',
                        'alternateFirstName',
                        'supervisorEmail',
                        'companyUserIdentifier',
                        'isSupervisor',
                        'isValidator',
                        'isActive',
                        'rgt',
                        'lft',
                        'hierarchy',
                        'defaultLang',
                        'notes',
                        'level',
                        'notify',
                        'companyId',
                        'syncId',
                        'supervisorId',
                        'externalId',
                        'approverId',
                        'defaultLocationId',
                        'addressId'
                    ],
                    'links' => [
                        'self',
                    ],
                    'relationships' => [
                        'assets' => [
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
                        'address' => [
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
                            'address',
                            'city',
                            'state',
                            'country',
                            'postalCode',
                            'created_at',
                            'updated_at'
                        ],
                        'links' => [
                            'self'
                        ]
                    ],
                    1 => [
                        'type',
                        'id',
                        'attributes' => [
                            'identification',
                            'active',
                            'statusId',
                            'typeId',
                            'externalId',
                            'carrierId',
                            'syncId',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],

                ],
            ]);
    }

    public function testGetUserByIdandIncludesDevices()
    {
        $user = factory(\WA\DataStore\User\User::class)->create();

        $device1 = factory(\WA\DataStore\Device\Device::class)->create()->id;
        $device2 = factory(\WA\DataStore\Device\Device::class)->create()->id;

        $datadevices = array($device1, $device2);

        $user->devices()->sync($datadevices);

        $res = $this->json('GET', 'users/'.$user->id.'?include=devices')
        //Log::debug("Users/id: ".print_r($res->response->getContent(), true));
            ->seeJsonStructure([
                'data' => [
                    'type',
                    'id',
                    'attributes' => [
                        'uuid',
                        'identification',
                        'email',
                        'alternateEmail',
                        'password',
                        'username',
                        'confirmation_code',
                        'remember_token',
                        'confirmed',
                        'firstName',
                        'lastName',
                        'alternateFirstName',
                        'supervisorEmail',
                        'companyUserIdentifier',
                        'isSupervisor',
                        'isValidator',
                        'isActive',
                        'rgt',
                        'lft',
                        'hierarchy',
                        'defaultLang',
                        'notes',
                        'level',
                        'notify',
                        'companyId',
                        'syncId',
                        'supervisorId',
                        'externalId',
                        'approverId',
                        'defaultLocationId',
                        'addressId'
                    ],
                    'links' => [
                        'self',
                    ],
                    'relationships' => [
                        'address' => [
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
                        'devices' => [
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
                    2 => [
                        'type',
                        'id',
                        'attributes' => [
                            'address',
                            'city',
                            'state',
                            'country',
                            'postalCode',
                            'created_at',
                            'updated_at'
                        ],
                        'links' => [
                            'self'
                        ]
                    ],
                    3 => [
                        'type',
                        'id',
                        'attributes' => [
                            'identification',
                            'name',
                            'properties',
                            'externalId',
                            'statusId',
                            'syncId',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                ],
            ]);
    }

    public function testGetUserByIdandIncludesRoles()
    {
        $user = factory(\WA\DataStore\User\User::class)->create();

        $role1 = factory(\WA\DataStore\Role\Role::class)->create()->id;
        $role2 = factory(\WA\DataStore\Role\Role::class)->create()->id;

        $dataroles = array($role1, $role2);

        $user->roles()->sync($dataroles);

        $res = $this->json('GET', 'users/'.$user->id.'?include=roles')
        //Log::debug("Users/id: ".print_r($res->response->getContent(), true));
            ->seeJsonStructure([
                'data' => [
                    'type',
                    'id',
                    'attributes' => [
                        'uuid',
                        'identification',
                        'email',
                        'alternateEmail',
                        'password',
                        'username',
                        'confirmation_code',
                        'remember_token',
                        'confirmed',
                        'firstName',
                        'lastName',
                        'alternateFirstName',
                        'supervisorEmail',
                        'companyUserIdentifier',
                        'isSupervisor',
                        'isValidator',
                        'isActive',
                        'rgt',
                        'lft',
                        'hierarchy',
                        'defaultLang',
                        'notes',
                        'level',
                        'notify',
                        'companyId',
                        'syncId',
                        'supervisorId',
                        'externalId',
                        'approverId',
                        'defaultLocationId',
                        'addressId'
                    ],
                    'links' => [
                        'self',
                    ],
                    'relationships' => [
                        'address' => [
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
                        'roles' => [
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
                            'address',
                            'city',
                            'state',
                            'country',
                            'postalCode',
                            'created_at',
                            'updated_at'
                        ],
                        'links' => [
                            'self'
                        ]
                    ],
                    1 => [
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
            ]);
    }

    public function testGetUserByIdandIncludesUdls()
    {
        $user = factory(\WA\DataStore\User\User::class)->create();

        $company1 = factory(\WA\DataStore\Company\Company::class)->create()->id;
        $company2 = factory(\WA\DataStore\Company\Company::class)->create()->id;

        $udl1 = factory(\WA\DataStore\Udl\Udl::class)->create(['companyId' => $company1])->id;
        $udl2 = factory(\WA\DataStore\Udl\Udl::class)->create(['companyId' => $company2])->id;

        $udlV1 = factory(\WA\DataStore\UdlValue\UdlValue::class)->create(['udlId' => $udl1])->id;
        $udlV2 = factory(\WA\DataStore\UdlValue\UdlValue::class)->create(['udlId' => $udl2])->id;

        $dataudls = array($udlV1, $udlV2);

        $user->udlValues()->sync($dataudls);

        $res = $this->json('GET', 'users/'.$user->id.'?include=udls')
        //Log::debug("testGetUserByIdandIncludesUdls: ".print_r($res->response->getContent(), true));
            ->seeJsonStructure([
                'data' => [
                    'type',
                    'id',
                    'attributes' => [
                        'uuid',
                        'identification',
                        'email',
                        'alternateEmail',
                        'password',
                        'username',
                        'confirmation_code',
                        'remember_token',
                        'confirmed',
                        'firstName',
                        'lastName',
                        'alternateFirstName',
                        'supervisorEmail',
                        'companyUserIdentifier',
                        'isSupervisor',
                        'isValidator',
                        'isActive',
                        'rgt',
                        'lft',
                        'hierarchy',
                        'defaultLang',
                        'notes',
                        'level',
                        'notify',
                        'companyId',
                        'syncId',
                        'supervisorId',
                        'externalId',
                        'approverId',
                        'defaultLocationId',
                        'addressId'
                    ],
                    'links' => [
                        'self',
                    ],
                    'relationships' => [
                        'address' => [
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
                            ],
                        ],
                    ],
                ],
                'included' => [
                    0 => [
                        'type',
                        'id',
                        'attributes' => [
                            'address',
                            'city',
                            'state',
                            'country',
                            'postalCode'
                        ],
                        'links' => [
                            'self'
                        ]
                    ],
                    1 => [
                        'type',
                        'id',
                        'attributes' => [
                            'udlId',
                            'udlValue'
                        ],
                        'links' => [
                            'self',
                        ],
                    ],

                ],
            ]);
    }

    public function testGetUserByIdandIncludesCompanies()
    {
        $companyId = factory(\WA\DataStore\Company\Company::class)->create()->id;

        $user = factory(\WA\DataStore\User\User::class)->create(['companyId' => $companyId]);

        $res = $this->json('GET', 'users/'.$user->id.'?include=companies')
        //Log::debug("Users/id: ".print_r($res->response->getContent(), true));
            ->seeJsonStructure([
                'data' => [
                    'type',
                    'id',
                    'attributes' => [
                        'uuid',
                        'identification',
                        'email',
                        'alternateEmail',
                        'password',
                        'username',
                        'confirmation_code',
                        'remember_token',
                        'confirmed',
                        'firstName',
                        'lastName',
                        'alternateFirstName',
                        'supervisorEmail',
                        'companyUserIdentifier',
                        'isSupervisor',
                        'isValidator',
                        'isActive',
                        'rgt',
                        'lft',
                        'hierarchy',
                        'defaultLang',
                        'notes',
                        'level',
                        'notify',
                        'companyId',
                        'syncId',
                        'supervisorId',
                        'externalId',
                        'approverId',
                        'defaultLocationId',
                        'addressId'
                    ],
                    'links' => [
                        'self',
                    ],
                    'relationships' => [
                        'address' => [
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
                        'companies' => [
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
                            'address',
                            'city',
                            'state',
                            'country',
                            'postalCode',
                            'created_at',
                            'updated_at'
                        ],
                        'links' => [
                            'self'
                        ]
                    ],
                    1 => [
                        'type',
                        'id',
                        'attributes' => [
                            'name',
                            'label',
                            'active',
                            'assetPath',
                            'currentBillMonth',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],

                ],
            ]);
    }

    public function testGetUserByIdandIncludesAllocations()
    {
        $user = factory(\WA\DataStore\User\User::class)->create();

        $allocation1 = factory(\WA\DataStore\Allocation\Allocation::class)->create(['userId' => $user->id]);
        $carrier1 = factory(\WA\DataStore\Carrier\Carrier::class)->create();
        $allocation1->carriers()->associate($carrier1);
        $allocation1->save();

        $allocation2 = factory(\WA\DataStore\Allocation\Allocation::class)->create(['userId' => $user->id]);
        $carrier2 = factory(\WA\DataStore\Carrier\Carrier::class)->create();
        $allocation2->carriers()->associate($carrier2);
        $allocation2->save();

        $res = $this->json('GET', 'users/'.$user->id.'?include=allocations')
        //Log::debug("Users/id: ".print_r($res->response->getContent(), true));
            ->seeJsonStructure([
                'data' => [
                    'type',
                    'id',
                    'attributes' => [
                        'uuid',
                        'identification',
                        'email',
                        'alternateEmail',
                        'password',
                        'username',
                        'confirmation_code',
                        'remember_token',
                        'confirmed',
                        'firstName',
                        'lastName',
                        'alternateFirstName',
                        'supervisorEmail',
                        'companyUserIdentifier',
                        'isSupervisor',
                        'isValidator',
                        'isActive',
                        'rgt',
                        'lft',
                        'hierarchy',
                        'defaultLang',
                        'notes',
                        'level',
                        'notify',
                        'companyId',
                        'syncId',
                        'supervisorId',
                        'externalId',
                        'approverId',
                        'defaultLocationId',
                        'addressId'
                    ],
                    'links' => [
                        'self',
                    ],
                    'relationships' => [
                        'address' => [
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
                        'allocations' => [
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
                            'address',
                            'city',
                            'state',
                            'country',
                            'postalCode',
                            'created_at',
                            'updated_at'
                        ],
                        'links' => [
                            'self'
                        ]
                    ],
                    1 => [
                        'type',
                        'id',
                        'attributes' => [
                            'bill_month',
                            'carrier',
                            'mobile_number',
                            'currency',
                            'device',
                            'allocated_charge',
                            'service_plan_charge',
                            'usage_charge',
                            'other_charge',
                            'fees',
                            'last_upgrade',
                        ],
                        'links' => [
                            'self',
                        ],
                    ]
                ]
            ]);
    }

    public function testGetUserByIdandIncludesContents()
    {
        $user = factory(\WA\DataStore\User\User::class)->create();

        $content1 = factory(\WA\DataStore\Content\Content::class)->create(['owner_id' => $user->id])->id;
        $content2 = factory(\WA\DataStore\Content\Content::class)->create(['owner_id' => $user->id])->id;

        $res = $this->json('GET', 'users/'.$user->id.'?include=contents')
        //Log::debug("Users/id: ".print_r($res->response->getContent(), true));
            ->seeJsonStructure([
                'data' => [
                    'type',
                    'id',
                    'attributes' => [
                        'uuid',
                        'identification',
                        'email',
                        'alternateEmail',
                        'password',
                        'username',
                        'confirmation_code',
                        'remember_token',
                        'confirmed',
                        'firstName',
                        'lastName',
                        'alternateFirstName',
                        'supervisorEmail',
                        'companyUserIdentifier',
                        'isSupervisor',
                        'isValidator',
                        'isActive',
                        'rgt',
                        'lft',
                        'hierarchy',
                        'defaultLang',
                        'notes',
                        'level',
                        'notify',
                        'companyId',
                        'syncId',
                        'supervisorId',
                        'externalId',
                        'approverId',
                        'defaultLocationId',
                        'addressId'
                    ],
                    'links' => [
                        'self',
                    ],
                    'relationships' => [
                        'address' => [
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
                        'contents' => [
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
                            'address',
                            'city',
                            'state',
                            'country',
                            'postalCode',
                            'created_at',
                            'updated_at'
                        ],
                        'links' => [
                            'self'
                        ]
                    ],
                    1 => [
                        'type',
                        'id',
                        'attributes' => [
                            'content',
                            'active',
                            'owner_type',
                            'owner_id'
                        ],
                        'links' => [
                            'self',
                        ],
                    ],

                ],
            ]);
    }

    public function testCreateUser()
    {
        $companyId = factory(\WA\DataStore\Company\Company::class)->create()->id;

        $addressId = factory(\WA\DataStore\Address\Address::class)->create()->id;

        $user = factory(\WA\DataStore\User\User::class)->create(['companyId' => $companyId, 'addressId' => $addressId]);
 
        $asset1 = factory(\WA\DataStore\Asset\Asset::class)->create()->id;
        $asset2 = factory(\WA\DataStore\Asset\Asset::class)->create()->id;

        $device1 = factory(\WA\DataStore\Device\Device::class)->create()->id;
        $device2 = factory(\WA\DataStore\Device\Device::class)->create()->id;

        $role1 = factory(\WA\DataStore\Role\Role::class)->create()->id;
        $role2 = factory(\WA\DataStore\Role\Role::class)->create()->id;

        $udl1 = factory(\WA\DataStore\Udl\Udl::class)->create(['companyId' => $companyId])->id;
        $udl2 = factory(\WA\DataStore\Udl\Udl::class)->create(['companyId' => $companyId])->id;

        $udlV1 = factory(\WA\DataStore\UdlValue\UdlValue::class)->create(['udlId' => $udl1])->id;
        $udlV2 = factory(\WA\DataStore\UdlValue\UdlValue::class)->create(['udlId' => $udl2])->id;

        $allocation1 = factory(\WA\DataStore\Allocation\Allocation::class)->create();
        $carrier1 = factory(\WA\DataStore\Carrier\Carrier::class)->create();
        $allocation1->carriers()->associate($carrier1);
        $allocation1->save();
        $allocation2 = factory(\WA\DataStore\Allocation\Allocation::class)->create();
        $allocation2->carriers()->associate($carrier1);
        $allocation2->save();


        $content1 = factory(\WA\DataStore\Content\Content::class)->create();
        $content2 = factory(\WA\DataStore\Content\Content::class)->create();

        $res = $this->json('POST', '/users?include=assets,devices,roles,udls,allocations,companies,contents',
            [
                'data' => [
                    'type' => 'users',
                    'attributes' => [
                        'uuid' => $user->uuid,
                        'email' => $user->email,
                        'alternateEmail' => $user->alternateEmail,
                        'password' => $user->password,
                        'username' => $user->username,
                        'confirmation_code' => $user->confirmation_code,
                        'remember_token' => $user->remember_token,
                        'confirmed' => $user->confirmed,
                        'firstName' => $user->firstName,
                        'lastName' => $user->lastName,
                        'alternateFirstName' => $user->alternateFirstName,
                        'supervisorEmail' => $user->supervisorEmail,
                        'companyUserIdentifier' => $user->companyUserIdentifier,
                        'isSupervisor' => $user->isSupervisor,
                        'isValidator' => $user->isValidator,
                        'isActive' => $user->isActive,
                        'rgt' => $user->rgt,
                        'lft' => $user->lft,
                        'hierarchy' => $user->hierarchy,
                        'defaultLang' => $user->defaultLang,
                        'notes' => $user->notes,
                        'level' => $user->level,
                        'notify' => $user->notify,
                        'companyId' => $user->companyId,
                        'syncId' => $user->syncId,
                        'supervisorId' => $user->supervisorId,
                        'externalId' => $user->externalId,
                        'approverId' => $user->approverId,
                        'defaultLocationId' => $user->defaultLocationId,
                        'addressId' => $user->addressId,
                    ],
                    'relationships' => [
                        'assets' => [
                            'data' => [
                                ['type' => 'assets', 'id' => $asset1],
                                ['type' => 'assets', 'id' => $asset2],
                            ],
                        ],
                        'devices' => [
                            'data' => [
                                ['type' => 'devices', 'id' => $device1],
                                ['type' => 'devices', 'id' => $device2],
                            ],
                        ],
                        'roles' => [
                            'data' => [
                                ['type' => 'roles', 'id' => $role1],
                                ['type' => 'roles', 'id' => $role2]
                            ],
                        ],
                        'udls' => [
                            'data' => [
                                ['type' => 'udls', 'id' => $udlV1],
                                ['type' => 'udls', 'id' => $udlV2],
                            ],
                        ],
                        'allocations' => [
                            'data' => [
                                [
                                    'billMonth' => $allocation1->billMonth,
                                    'mobileNumber' => $allocation1->mobileNumber,
                                    'carrier' => $allocation1->carrier,
                                    'currency' => $allocation1->currency,
                                    'handsetModel' => $allocation1->handsetModel,
                                    'totalAllocatedCharge' => $allocation1->totalAllocatedCharge,
                                    'preAllocatedAmountDue' => $allocation1->preAllocatedAmountDue,
                                    'otherAdjustments' => $allocation1->otherAdjustments,
                                    'preAdjustedAccessCharge' => $allocation1->preAdjustedAccessCharge,
                                    'adjustedAccessCost' => $allocation1->adjustedAccessCost,
                                    'bBCost' => $allocation1->bBCost,
                                    'pDACost' => $allocation1->pDACost,
                                    'iPhoneCost' => $allocation1->iPhoneCost,
                                    'featuresCost' => $allocation1->featuresCost,
                                    'dataCardCost' => $allocation1->dataCardCost,
                                    'lDCanadaCost' => $allocation1->lDCanadaCost,
                                    'uSAddOnPlanCost' => $allocation1->uSAddOnPlanCost,
                                    'uSLDAddOnPlanCost' => $allocation1->uSLDAddOnPlanCost,
                                    'uSDataRoamingCost' => $allocation1->uSDataRoamingCost,
                                    'nightAndWeekendAddOnCost' => $allocation1->nightAndWeekendAddOnCost,
                                    'minuteAddOnCost' => $allocation1->minuteAddOnCost,
                                    'servicePlanCharges' => $allocation1->servicePlanCharges,
                                    'directConnectCost' => $allocation1->directConnectCost,
                                    'textMessagingCost' => $allocation1->textMessagingCost,
                                    'dataCost' => $allocation1->dataCost,
                                    'intlRoamingCost' => $allocation1->intlRoamingCost,
                                    'intlLongDistanceCost' => $allocation1->intlLongDistanceCost,
                                    'directoryAssistanceCost' => $allocation1->directoryAssistanceCost,
                                    'callForwardingCost' => $allocation1->callForwardingCost,
                                    'airtimeCost' => $allocation1->airtimeCost,
                                    'usageCharges' => $allocation1->usageCharges,
                                    'equipmentCost' => $allocation1->equipmentCost,
                                    'otherDiscountChargesCost' => $allocation1->otherDiscountChargesCost,
                                    'taxes' => $allocation1->taxes,
                                    'thirdPartyCost' => $allocation1->thirdPartyCost,
                                    'otherCharges' => $allocation1->otherCharges,
                                    'waFees' => $allocation1->waFees,
                                    'lineFees' => $allocation1->lineFees,
                                    'mobilityFees' => $allocation1->mobilityFees,
                                    'fees' => $allocation1->fees,
                                    'last_upgrade' => $allocation1->last_upgrade
                                ],
                                [
                                    'billMonth' => $allocation2->billMonth,
                                    'mobileNumber' => $allocation2->mobileNumber,
                                    'carrier' => $allocation2->carrier,
                                    'currency' => $allocation2->currency,
                                    'handsetModel' => $allocation2->handsetModel,
                                    'totalAllocatedCharge' => $allocation2->totalAllocatedCharge,
                                    'preAllocatedAmountDue' => $allocation2->preAllocatedAmountDue,
                                    'otherAdjustments' => $allocation2->otherAdjustments,
                                    'preAdjustedAccessCharge' => $allocation2->preAdjustedAccessCharge,
                                    'adjustedAccessCost' => $allocation2->adjustedAccessCost,
                                    'bBCost' => $allocation2->bBCost,
                                    'pDACost' => $allocation2->pDACost,
                                    'iPhoneCost' => $allocation2->iPhoneCost,
                                    'featuresCost' => $allocation2->featuresCost,
                                    'dataCardCost' => $allocation2->dataCardCost,
                                    'lDCanadaCost' => $allocation2->lDCanadaCost,
                                    'uSAddOnPlanCost' => $allocation2->uSAddOnPlanCost,
                                    'uSLDAddOnPlanCost' => $allocation2->uSLDAddOnPlanCost,
                                    'uSDataRoamingCost' => $allocation2->uSDataRoamingCost,
                                    'nightAndWeekendAddOnCost' => $allocation2->nightAndWeekendAddOnCost,
                                    'minuteAddOnCost' => $allocation2->minuteAddOnCost,
                                    'servicePlanCharges' => $allocation2->servicePlanCharges,
                                    'directConnectCost' => $allocation2->directConnectCost,
                                    'textMessagingCost' => $allocation2->textMessagingCost,
                                    'dataCost' => $allocation2->dataCost,
                                    'intlRoamingCost' => $allocation2->intlRoamingCost,
                                    'intlLongDistanceCost' => $allocation2->intlLongDistanceCost,
                                    'directoryAssistanceCost' => $allocation2->directoryAssistanceCost,
                                    'callForwardingCost' => $allocation2->callForwardingCost,
                                    'airtimeCost' => $allocation2->airtimeCost,
                                    'usageCharges' => $allocation2->usageCharges,
                                    'equipmentCost' => $allocation2->equipmentCost,
                                    'otherDiscountChargesCost' => $allocation2->otherDiscountChargesCost,
                                    'taxes' => $allocation2->taxes,
                                    'thirdPartyCost' => $allocation2->thirdPartyCost,
                                    'otherCharges' => $allocation2->otherCharges,
                                    'waFees' => $allocation2->waFees,
                                    'lineFees' => $allocation2->lineFees,
                                    'mobilityFees' => $allocation2->mobilityFees,
                                    'fees' => $allocation2->fees,
                                    'last_upgrade' => $allocation2->last_upgrade
                                ]

                            ]
                        ],
                        'contents' => [
                            'data' => [
                                [
                                    'content' => $content1->content,
                                    'active' => $content1->active,
                                    'owner_type' => $content1->owner_type
                                ],
                                [
                                    'content' => $content2->content,
                                    'active' => $content2->active,
                                    'owner_type' => $content2->owner_type
                                ]
                            ]
                        ]
                    ],
                ],
            ]
            )
            //Log::debug("testCreateUser: ".print_r($res->response->getContent(), true));
            ->seeJson(
                [
                    'uuid' => $user->uuid,
                    'email' => $user->email,
                    'alternateEmail' => $user->alternateEmail,
                    'password' => $user->password,
                    'username' => $user->username,
                    'confirmation_code' => $user->confirmation_code,
                    'remember_token' => $user->remember_token,
                    'confirmed' => $user->confirmed,
                    'firstName' => $user->firstName,
                    'lastName' => $user->lastName,
                    'alternateFirstName' => $user->alternateFirstName,
                    'supervisorEmail' => $user->supervisorEmail,
                    'companyUserIdentifier' => $user->companyUserIdentifier,
                    'isSupervisor' => $user->isSupervisor,
                    'isValidator' => $user->isValidator,
                    'isActive' => $user->isActive,
                    'rgt' => $user->rgt,
                    'lft' => $user->lft,
                    'hierarchy' => $user->hierarchy,
                    'defaultLang' => $user->defaultLang,
                    'notes' => $user->notes,
                    'level' => $user->level,
                    'notify' => $user->notify,
                    'companyId' => $user->companyId,
                    'syncId' => $user->syncId,
                    'supervisorId' => $user->supervisorId,
                    'externalId' => $user->externalId,
                    'approverId' => $user->approverId,
                    'defaultLocationId' => $user->defaultLocationId,
                    'addressId' => $user->addressId,
                ])
            ->seeJsonStructure(
                [
                    'data' => [
                        'type',
                        'id',
                        'attributes' => [
                            'uuid',
                            'email',
                            'alternateEmail',
                            'password',
                            'username',
                            'confirmation_code',
                            'remember_token',
                            'confirmed',
                            'firstName',
                            'lastName',
                            'alternateFirstName',
                            'supervisorEmail',
                            'companyUserIdentifier',
                            'isSupervisor',
                            'isValidator',
                            'isActive',
                            'rgt',
                            'lft',
                            'hierarchy',
                            'defaultLang',
                            'notes',
                            'level',
                            'notify',
                            'companyId',
                            'syncId',
                            'supervisorId',
                            'externalId',
                            'approverId',
                            'defaultLocationId',
                            'addressId',
                        ],
                        'links' => [
                            'self'
                        ],
                        'relationships' => [
                            'address' => [
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
                            ],
                            'assets' => [
                                'links' => [
                                    'self',
                                    'related'
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
                            ],
                            'devices' => [
                                'links' => [
                                    'self',
                                    'related'
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
                            ],
                            'companies' => [
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
                            ],
                            'roles' => [
                                'links' => [
                                    'self',
                                    'related'
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
                            ],
                            'allocations' => [
                                'links' => [
                                    'self',
                                    'related'
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
                            ],
                            'contents' => [
                                'links' => [
                                    'self',
                                    'related'
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
                            ],
                            'udls' => [
                                'links' => [
                                    'self',
                                    'related'
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
                                'make',
                                'model',
                                'class',
                                'deviceOS',
                                'description',
                                'statusId',
                                'image'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        1 => [
                            'type',
                            'id',
                            'attributes' => [
                                'make',
                                'model',
                                'class',
                                'deviceOS',
                                'description',
                                'statusId',
                                'image'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        2 => [
                            'type',
                            'id',
                            'attributes' => [
                                'address',
                                'city',
                                'state',
                                'country',
                                'postalCode'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        3 => [
                            'type',
                            'id',
                            'attributes' => [
                                'identification',
                                'active',
                                'statusId',
                                'typeId',
                                'externalId',
                                'carrierId',
                                'syncId'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        4 => [
                            'type',
                            'id',
                            'attributes' => [
                                'identification',
                                'active',
                                'statusId',
                                'typeId',
                                'externalId',
                                'carrierId',
                                'syncId'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        5 => [
                            'type',
                            'id',
                            'attributes' => [
                                'identification',
                                'name',
                                'properties',
                                'externalId',
                                'statusId',
                                'syncId'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        6 => [
                            'type',
                            'id',
                            'attributes' => [
                                'identification',
                                'name',
                                'properties',
                                'externalId',
                                'statusId',
                                'syncId'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        7 => [
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
                                'currentBillMonth'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        8 => [
                            'type',
                            'id',
                            'attributes' => [
                                'name'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        9 => [
                            'type',
                            'id',
                            'attributes' => [
                                'name'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        10 => [
                            'type',
                            'id',
                            'attributes' => [
                                'bill_month',
                                'carrier',
                                'mobile_number',
                                'currency',
                                'device',
                                'allocated_charge',
                                'service_plan_charge',
                                'usage_charge',
                                'other_charge',
                                'fees',
                                'last_upgrade'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        11 => [
                            'type',
                            'id',
                            'attributes' => [
                                'bill_month',
                                'carrier',
                                'mobile_number',
                                'currency',
                                'device',
                                'allocated_charge',
                                'service_plan_charge',
                                'usage_charge',
                                'other_charge',
                                'fees',
                                'last_upgrade'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        12 => [
                            'type',
                            'id',
                            'attributes' => [
                                'content',
                                'active',
                                'owner_type',
                                'owner_id'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        13 => [
                            'type',
                            'id',
                            'attributes' => [
                                'content',
                                'active',
                                'owner_type',
                                'owner_id'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        14 => [
                            'type',
                            'id',
                            'attributes' => [
                                'udlId',
                                'udlValue'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        15 => [
                            'type',
                            'id',
                            'attributes' => [
                                'udlId',
                                'udlValue'
                            ],
                            'links' => [
                                'self'
                            ]
                        ]
                    ]
                ]);
    }

    public function testCreateUserReturnNoValidData()
    {
        // 'data' no valid.
        $user = $this->json('POST', '/users',
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

    public function testCreateUserReturnNoValidType()
    {
        // 'type' no valid.
        $user = $this->json('POST', '/users',
            [
                'data' => [
                    'NoValid' => 'users',
                    'attributes' => [
                        'email' => 'email@prueba.com',
                        'username' => 'username',
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

    public function testCreateUserReturnNoValidAttributes()
    {
        // 'attributes' no valid.
        $user = $this->json('POST', '/users',
            [
                'data' => [
                    'type' => 'users',
                    'NoValid' => [
                        'email' => 'email@prueba.com',
                        'username' => 'username',
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

    public function testCreateUserReturnRelationshipNoExists()
    {
        $companyId = factory(\WA\DataStore\Company\Company::class)->create()->id;

        $addressId = factory(\WA\DataStore\Address\Address::class)->create()->id;

        $user = factory(\WA\DataStore\User\User::class)->create(['companyId' => $companyId, 'addressId' => $addressId]);

        $res = $this->json('POST', '/users?include=assets',
            [
                'data' => [
                    'type' => 'users',
                    'attributes' => [
                        'uuid' => $user->uuid,
                        'email' => $user->email,
                        'alternateEmail' => $user->alternateEmail,
                        'password' => $user->password,
                        'username' => $user->username,
                        'confirmation_code' => $user->confirmation_code,
                        'remember_token' => $user->remember_token,
                        'confirmed' => $user->confirmed,
                        'firstName' => $user->firstName,
                        'lastName' => $user->lastName,
                        'alternateFirstName' => $user->alternateFirstName,
                        'supervisorEmail' => $user->supervisorEmail,
                        'companyUserIdentifier' => $user->companyUserIdentifier,
                        'isSupervisor' => $user->isSupervisor,
                        'isValidator' => $user->isValidator,
                        'isActive' => $user->isActive,
                        'rgt' => $user->rgt,
                        'lft' => $user->lft,
                        'hierarchy' => $user->hierarchy,
                        'defaultLang' => $user->defaultLang,
                        'notes' => $user->notes,
                        'level' => $user->level,
                        'notify' => $user->notify,
                        'companyId' => $user->companyId,
                        'syncId' => $user->syncId,
                        'supervisorId' => $user->supervisorId,
                        'externalId' => $user->externalId,
                        'approverId' => $user->approverId,
                        'defaultLocationId' => $user->defaultLocationId,
                        'addressId' => $user->addressId,
                    ],
                    'NoRelationships' => [
                        'assets' => [
                            'data' => [
                                ['type' => 'assets', 'id' => 1],
                                ['type' => 'assets', 'id' => 2],
                            ],
                        ]
                    ],
                ],
            ]
            )
            //Log::debug("Users/id: ".print_r($res->response->getContent(), true));
            ->seeJson(
                [
                    'uuid' => $user->uuid,
                    'email' => $user->email,
                    'alternateEmail' => $user->alternateEmail,
                    'password' => $user->password,
                    'username' => $user->username,
                    'confirmation_code' => $user->confirmation_code,
                    'remember_token' => $user->remember_token,
                    'confirmed' => $user->confirmed,
                    'firstName' => $user->firstName,
                    'lastName' => $user->lastName,
                    'alternateFirstName' => $user->alternateFirstName,
                    'supervisorEmail' => $user->supervisorEmail,
                    'companyUserIdentifier' => $user->companyUserIdentifier,
                    'isSupervisor' => $user->isSupervisor,
                    'isValidator' => $user->isValidator,
                    'isActive' => $user->isActive,
                    'rgt' => $user->rgt,
                    'lft' => $user->lft,
                    'hierarchy' => $user->hierarchy,
                    'defaultLang' => $user->defaultLang,
                    'notes' => $user->notes,
                    'level' => $user->level,
                    'notify' => $user->notify,
                    'companyId' => $user->companyId,
                    'syncId' => $user->syncId,
                    'supervisorId' => $user->supervisorId,
                    'externalId' => $user->externalId,
                    'approverId' => $user->approverId,
                    'defaultLocationId' => $user->defaultLocationId,
                    'addressId' => $user->addressId,
                ])
            ->seeJsonStructure(
                [
                    'data' => [
                        'type',
                        'id',
                        'attributes' => [
                            'uuid',
                            'email',
                            'alternateEmail',
                            'password',
                            'username',
                            'confirmation_code',
                            'remember_token',
                            'confirmed',
                            'firstName',
                            'lastName',
                            'alternateFirstName',
                            'supervisorEmail',
                            'companyUserIdentifier',
                            'isSupervisor',
                            'isValidator',
                            'isActive',
                            'rgt',
                            'lft',
                            'hierarchy',
                            'defaultLang',
                            'notes',
                            'level',
                            'notify',
                            'companyId',
                            'syncId',
                            'supervisorId',
                            'externalId',
                            'approverId',
                            'defaultLocationId',
                            'addressId',
                        ],
                        'links' => [
                            'self'
                        ],
                        'relationships' => [
                            'address' => [
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
                            ],
                            'assets' => [
                                'links' => [
                                    'self',
                                    'related'
                                ],
                                'data' => []
                            ]
                        ]
                    ],
                    'included' => [
                        0 => [
                            'type',
                            'id',
                            'attributes' => [
                                'address',
                                'city',
                                'state',
                                'country',
                                'postalCode'
                            ],
                            'links' => [
                                'self'
                            ]
                        ]
                    ]
                ]);
    }

    public function testCreateUserReturnRelationshipNoExistsInclude()
    {
        $companyId = factory(\WA\DataStore\Company\Company::class)->create()->id;

        $addressId = factory(\WA\DataStore\Address\Address::class)->create()->id;

        $user = factory(\WA\DataStore\User\User::class)->create(['companyId' => $companyId, 'addressId' => $addressId]);

        $res = $this->json('POST', '/users?include=assets',
            [
                'data' => [
                    'type' => 'users',
                    'attributes' => [
                        'uuid' => $user->uuid,
                        'email' => $user->email,
                        'alternateEmail' => $user->alternateEmail,
                        'password' => $user->password,
                        'username' => $user->username,
                        'confirmation_code' => $user->confirmation_code,
                        'remember_token' => $user->remember_token,
                        'confirmed' => $user->confirmed,
                        'firstName' => $user->firstName,
                        'lastName' => $user->lastName,
                        'alternateFirstName' => $user->alternateFirstName,
                        'supervisorEmail' => $user->supervisorEmail,
                        'companyUserIdentifier' => $user->companyUserIdentifier,
                        'isSupervisor' => $user->isSupervisor,
                        'isValidator' => $user->isValidator,
                        'isActive' => $user->isActive,
                        'rgt' => $user->rgt,
                        'lft' => $user->lft,
                        'hierarchy' => $user->hierarchy,
                        'defaultLang' => $user->defaultLang,
                        'notes' => $user->notes,
                        'level' => $user->level,
                        'notify' => $user->notify,
                        'companyId' => $user->companyId,
                        'syncId' => $user->syncId,
                        'supervisorId' => $user->supervisorId,
                        'externalId' => $user->externalId,
                        'approverId' => $user->approverId,
                        'defaultLocationId' => $user->defaultLocationId,
                        'addressId' => $user->addressId,
                    ],
                    'relationships' => [
                        'NoAssets' => [
                            'data' => [
                                ['type' => 'assets', 'id' => 1],
                                ['type' => 'assets', 'id' => 2],
                            ],
                        ]
                    ],
                ],
            ]
            )
            //Log::debug("Users/id: ".print_r($res->response->getContent(), true));
            ->seeJson(
                [
                    'uuid' => $user->uuid,
                    'email' => $user->email,
                    'alternateEmail' => $user->alternateEmail,
                    'password' => $user->password,
                    'username' => $user->username,
                    'confirmation_code' => $user->confirmation_code,
                    'remember_token' => $user->remember_token,
                    'confirmed' => $user->confirmed,
                    'firstName' => $user->firstName,
                    'lastName' => $user->lastName,
                    'alternateFirstName' => $user->alternateFirstName,
                    'supervisorEmail' => $user->supervisorEmail,
                    'companyUserIdentifier' => $user->companyUserIdentifier,
                    'isSupervisor' => $user->isSupervisor,
                    'isValidator' => $user->isValidator,
                    'isActive' => $user->isActive,
                    'rgt' => $user->rgt,
                    'lft' => $user->lft,
                    'hierarchy' => $user->hierarchy,
                    'defaultLang' => $user->defaultLang,
                    'notes' => $user->notes,
                    'level' => $user->level,
                    'notify' => $user->notify,
                    'companyId' => $user->companyId,
                    'syncId' => $user->syncId,
                    'supervisorId' => $user->supervisorId,
                    'externalId' => $user->externalId,
                    'approverId' => $user->approverId,
                    'defaultLocationId' => $user->defaultLocationId,
                    'addressId' => $user->addressId,
                ])
            ->seeJsonStructure(
                [
                    'data' => [
                        'type',
                        'id',
                        'attributes' => [
                            'uuid',
                            'email',
                            'alternateEmail',
                            'password',
                            'username',
                            'confirmation_code',
                            'remember_token',
                            'confirmed',
                            'firstName',
                            'lastName',
                            'alternateFirstName',
                            'supervisorEmail',
                            'companyUserIdentifier',
                            'isSupervisor',
                            'isValidator',
                            'isActive',
                            'rgt',
                            'lft',
                            'hierarchy',
                            'defaultLang',
                            'notes',
                            'level',
                            'notify',
                            'companyId',
                            'syncId',
                            'supervisorId',
                            'externalId',
                            'approverId',
                            'defaultLocationId',
                            'addressId',
                        ],
                        'links' => [
                            'self'
                        ],
                        'relationships' => [
                            'address' => [
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
                            ],
                            'assets' => [
                                'links' => [
                                    'self',
                                    'related'
                                ],
                                'data' => []
                            ]
                        ]
                    ],
                    'included' => [
                        0 => [
                            'type',
                            'id',
                            'attributes' => [
                                'address',
                                'city',
                                'state',
                                'country',
                                'postalCode'
                            ],
                            'links' => [
                                'self'
                            ]
                        ]
                    ]
                ]);
    }

    public function testCreateUserReturnRelationshipNoData()
    {
        $companyId = factory(\WA\DataStore\Company\Company::class)->create()->id;

        $addressId = factory(\WA\DataStore\Address\Address::class)->create()->id;

        $user = factory(\WA\DataStore\User\User::class)->create(['companyId' => $companyId, 'addressId' => $addressId]);

        $res = $this->json('POST', '/users?include=assets',
            [
                'data' => [
                    'type' => 'users',
                    'attributes' => [
                        'uuid' => $user->uuid,
                        'email' => $user->email,
                        'alternateEmail' => $user->alternateEmail,
                        'password' => $user->password,
                        'username' => $user->username,
                        'confirmation_code' => $user->confirmation_code,
                        'remember_token' => $user->remember_token,
                        'confirmed' => $user->confirmed,
                        'firstName' => $user->firstName,
                        'lastName' => $user->lastName,
                        'alternateFirstName' => $user->alternateFirstName,
                        'supervisorEmail' => $user->supervisorEmail,
                        'companyUserIdentifier' => $user->companyUserIdentifier,
                        'isSupervisor' => $user->isSupervisor,
                        'isValidator' => $user->isValidator,
                        'isActive' => $user->isActive,
                        'rgt' => $user->rgt,
                        'lft' => $user->lft,
                        'hierarchy' => $user->hierarchy,
                        'defaultLang' => $user->defaultLang,
                        'notes' => $user->notes,
                        'level' => $user->level,
                        'notify' => $user->notify,
                        'companyId' => $user->companyId,
                        'syncId' => $user->syncId,
                        'supervisorId' => $user->supervisorId,
                        'externalId' => $user->externalId,
                        'approverId' => $user->approverId,
                        'defaultLocationId' => $user->defaultLocationId,
                        'addressId' => $user->addressId,
                    ],
                    'relationships' => [
                        'assets' => [
                            'NoData' => [
                                ['type' => 'NoAssets', 'id' => 1],
                                ['type' => 'NoAssets', 'id' => 2],
                            ],
                        ]
                    ],
                ],
            ]
            )
            //Log::debug("Users/id: ".print_r($res->response->getContent(), true));
            ->seeJson(
                [
                    'uuid' => $user->uuid,
                    'email' => $user->email,
                    'alternateEmail' => $user->alternateEmail,
                    'password' => $user->password,
                    'username' => $user->username,
                    'confirmation_code' => $user->confirmation_code,
                    'remember_token' => $user->remember_token,
                    'confirmed' => $user->confirmed,
                    'firstName' => $user->firstName,
                    'lastName' => $user->lastName,
                    'alternateFirstName' => $user->alternateFirstName,
                    'supervisorEmail' => $user->supervisorEmail,
                    'companyUserIdentifier' => $user->companyUserIdentifier,
                    'isSupervisor' => $user->isSupervisor,
                    'isValidator' => $user->isValidator,
                    'isActive' => $user->isActive,
                    'rgt' => $user->rgt,
                    'lft' => $user->lft,
                    'hierarchy' => $user->hierarchy,
                    'defaultLang' => $user->defaultLang,
                    'notes' => $user->notes,
                    'level' => $user->level,
                    'notify' => $user->notify,
                    'companyId' => $user->companyId,
                    'syncId' => $user->syncId,
                    'supervisorId' => $user->supervisorId,
                    'externalId' => $user->externalId,
                    'approverId' => $user->approverId,
                    'defaultLocationId' => $user->defaultLocationId,
                    'addressId' => $user->addressId,
                ])
            ->seeJsonStructure(
                [
                    'data' => [
                        'type',
                        'id',
                        'attributes' => [
                            'uuid',
                            'email',
                            'alternateEmail',
                            'password',
                            'username',
                            'confirmation_code',
                            'remember_token',
                            'confirmed',
                            'firstName',
                            'lastName',
                            'alternateFirstName',
                            'supervisorEmail',
                            'companyUserIdentifier',
                            'isSupervisor',
                            'isValidator',
                            'isActive',
                            'rgt',
                            'lft',
                            'hierarchy',
                            'defaultLang',
                            'notes',
                            'level',
                            'notify',
                            'companyId',
                            'syncId',
                            'supervisorId',
                            'externalId',
                            'approverId',
                            'defaultLocationId',
                            'addressId',
                        ],
                        'links' => [
                            'self'
                        ],
                        'relationships' => [
                            'address' => [
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
                            ],
                            'assets' => [
                                'links' => [
                                    'self',
                                    'related'
                                ],
                                'data' => []
                            ]
                        ]
                    ],
                    'included' => [
                        0 => [
                            'type',
                            'id',
                            'attributes' => [
                                'address',
                                'city',
                                'state',
                                'country',
                                'postalCode'
                            ],
                            'links' => [
                                'self'
                            ]
                        ]
                    ]
                ]);
    }

    public function testCreateUserReturnRelationshipNoCorrectType()
    {
        $companyId = factory(\WA\DataStore\Company\Company::class)->create()->id;

        $addressId = factory(\WA\DataStore\Address\Address::class)->create()->id;

        $user = factory(\WA\DataStore\User\User::class)->create(['companyId' => $companyId, 'addressId' => $addressId]);

        $res = $this->json('POST', '/users?include=assets',
            [
                'data' => [
                    'type' => 'users',
                    'attributes' => [
                        'uuid' => $user->uuid,
                        'email' => $user->email,
                        'alternateEmail' => $user->alternateEmail,
                        'password' => $user->password,
                        'username' => $user->username,
                        'confirmation_code' => $user->confirmation_code,
                        'remember_token' => $user->remember_token,
                        'confirmed' => $user->confirmed,
                        'firstName' => $user->firstName,
                        'lastName' => $user->lastName,
                        'alternateFirstName' => $user->alternateFirstName,
                        'supervisorEmail' => $user->supervisorEmail,
                        'companyUserIdentifier' => $user->companyUserIdentifier,
                        'isSupervisor' => $user->isSupervisor,
                        'isValidator' => $user->isValidator,
                        'isActive' => $user->isActive,
                        'rgt' => $user->rgt,
                        'lft' => $user->lft,
                        'hierarchy' => $user->hierarchy,
                        'defaultLang' => $user->defaultLang,
                        'notes' => $user->notes,
                        'level' => $user->level,
                        'notify' => $user->notify,
                        'companyId' => $user->companyId,
                        'syncId' => $user->syncId,
                        'supervisorId' => $user->supervisorId,
                        'externalId' => $user->externalId,
                        'approverId' => $user->approverId,
                        'defaultLocationId' => $user->defaultLocationId,
                        'addressId' => $user->addressId,
                    ],
                    'relationships' => [
                        'assets' => [
                            'data' => [
                                ['type' => 'NoAssets', 'id' => 1],
                                ['type' => 'NoAssets', 'id' => 2],
                            ],
                        ]
                    ],
                ],
            ]
            )
            //Log::debug("Users/id: ".print_r($res->response->getContent(), true));
            ->seeJson(
                [
                    'uuid' => $user->uuid,
                    'email' => $user->email,
                    'alternateEmail' => $user->alternateEmail,
                    'password' => $user->password,
                    'username' => $user->username,
                    'confirmation_code' => $user->confirmation_code,
                    'remember_token' => $user->remember_token,
                    'confirmed' => $user->confirmed,
                    'firstName' => $user->firstName,
                    'lastName' => $user->lastName,
                    'alternateFirstName' => $user->alternateFirstName,
                    'supervisorEmail' => $user->supervisorEmail,
                    'companyUserIdentifier' => $user->companyUserIdentifier,
                    'isSupervisor' => $user->isSupervisor,
                    'isValidator' => $user->isValidator,
                    'isActive' => $user->isActive,
                    'rgt' => $user->rgt,
                    'lft' => $user->lft,
                    'hierarchy' => $user->hierarchy,
                    'defaultLang' => $user->defaultLang,
                    'notes' => $user->notes,
                    'level' => $user->level,
                    'notify' => $user->notify,
                    'companyId' => $user->companyId,
                    'syncId' => $user->syncId,
                    'supervisorId' => $user->supervisorId,
                    'externalId' => $user->externalId,
                    'approverId' => $user->approverId,
                    'defaultLocationId' => $user->defaultLocationId,
                    'addressId' => $user->addressId,
                ])
            ->seeJsonStructure(
                [
                    'data' => [
                        'type',
                        'id',
                        'attributes' => [
                            'uuid',
                            'email',
                            'alternateEmail',
                            'password',
                            'username',
                            'confirmation_code',
                            'remember_token',
                            'confirmed',
                            'firstName',
                            'lastName',
                            'alternateFirstName',
                            'supervisorEmail',
                            'companyUserIdentifier',
                            'isSupervisor',
                            'isValidator',
                            'isActive',
                            'rgt',
                            'lft',
                            'hierarchy',
                            'defaultLang',
                            'notes',
                            'level',
                            'notify',
                            'companyId',
                            'syncId',
                            'supervisorId',
                            'externalId',
                            'approverId',
                            'defaultLocationId',
                            'addressId',
                        ],
                        'links' => [
                            'self'
                        ],
                        'relationships' => [
                            'address' => [
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
                            ],
                            'assets' => [
                                'links' => [
                                    'self',
                                    'related'
                                ],
                                'data' => []
                            ]
                        ]
                    ],
                    'included' => [
                        0 => [
                            'type',
                            'id',
                            'attributes' => [
                                'address',
                                'city',
                                'state',
                                'country',
                                'postalCode'
                            ],
                            'links' => [
                                'self'
                            ]
                        ]
                    ]
                ]);
    }

    public function testCreateUserReturnRelationshipNoIdExists()
    {
        $companyId = factory(\WA\DataStore\Company\Company::class)->create()->id;

        $addressId = factory(\WA\DataStore\Address\Address::class)->create()->id;

        $user = factory(\WA\DataStore\User\User::class)->create(['companyId' => $companyId, 'addressId' => $addressId]);

        $res = $this->json('POST', '/users?include=assets',
            [
                'data' => [
                    'type' => 'users',
                    'attributes' => [
                        'uuid' => $user->uuid,
                        'email' => $user->email,
                        'alternateEmail' => $user->alternateEmail,
                        'password' => $user->password,
                        'username' => $user->username,
                        'confirmation_code' => $user->confirmation_code,
                        'remember_token' => $user->remember_token,
                        'confirmed' => $user->confirmed,
                        'firstName' => $user->firstName,
                        'lastName' => $user->lastName,
                        'alternateFirstName' => $user->alternateFirstName,
                        'supervisorEmail' => $user->supervisorEmail,
                        'companyUserIdentifier' => $user->companyUserIdentifier,
                        'isSupervisor' => $user->isSupervisor,
                        'isValidator' => $user->isValidator,
                        'isActive' => $user->isActive,
                        'rgt' => $user->rgt,
                        'lft' => $user->lft,
                        'hierarchy' => $user->hierarchy,
                        'defaultLang' => $user->defaultLang,
                        'notes' => $user->notes,
                        'level' => $user->level,
                        'notify' => $user->notify,
                        'companyId' => $user->companyId,
                        'syncId' => $user->syncId,
                        'supervisorId' => $user->supervisorId,
                        'externalId' => $user->externalId,
                        'approverId' => $user->approverId,
                        'defaultLocationId' => $user->defaultLocationId,
                        'addressId' => $user->addressId,
                    ],
                    'relationships' => [
                        'assets' => [
                            'data' => [
                                ['type' => 'assets', 'NoId' => 1],
                                ['type' => 'assets', 'NoId' => 2],
                            ],
                        ]
                    ],
                ],
            ]
            )
            //Log::debug("Users/id: ".print_r($res->response->getContent(), true));
            ->seeJson(
                [
                    'uuid' => $user->uuid,
                    'email' => $user->email,
                    'alternateEmail' => $user->alternateEmail,
                    'password' => $user->password,
                    'username' => $user->username,
                    'confirmation_code' => $user->confirmation_code,
                    'remember_token' => $user->remember_token,
                    'confirmed' => $user->confirmed,
                    'firstName' => $user->firstName,
                    'lastName' => $user->lastName,
                    'alternateFirstName' => $user->alternateFirstName,
                    'supervisorEmail' => $user->supervisorEmail,
                    'companyUserIdentifier' => $user->companyUserIdentifier,
                    'isSupervisor' => $user->isSupervisor,
                    'isValidator' => $user->isValidator,
                    'isActive' => $user->isActive,
                    'rgt' => $user->rgt,
                    'lft' => $user->lft,
                    'hierarchy' => $user->hierarchy,
                    'defaultLang' => $user->defaultLang,
                    'notes' => $user->notes,
                    'level' => $user->level,
                    'notify' => $user->notify,
                    'companyId' => $user->companyId,
                    'syncId' => $user->syncId,
                    'supervisorId' => $user->supervisorId,
                    'externalId' => $user->externalId,
                    'approverId' => $user->approverId,
                    'defaultLocationId' => $user->defaultLocationId,
                    'addressId' => $user->addressId,
                ])
            ->seeJsonStructure(
                [
                    'data' => [
                        'type',
                        'id',
                        'attributes' => [
                            'uuid',
                            'email',
                            'alternateEmail',
                            'password',
                            'username',
                            'confirmation_code',
                            'remember_token',
                            'confirmed',
                            'firstName',
                            'lastName',
                            'alternateFirstName',
                            'supervisorEmail',
                            'companyUserIdentifier',
                            'isSupervisor',
                            'isValidator',
                            'isActive',
                            'rgt',
                            'lft',
                            'hierarchy',
                            'defaultLang',
                            'notes',
                            'level',
                            'notify',
                            'companyId',
                            'syncId',
                            'supervisorId',
                            'externalId',
                            'approverId',
                            'defaultLocationId',
                            'addressId',
                        ],
                        'links' => [
                            'self'
                        ],
                        'relationships' => [
                            'address' => [
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
                            ],
                            'assets' => [
                                'links' => [
                                    'self',
                                    'related'
                                ],
                                'data' => []
                            ]
                        ]
                    ],
                    'included' => [
                        0 => [
                            'type',
                            'id',
                            'attributes' => [
                                'address',
                                'city',
                                'state',
                                'country',
                                'postalCode'
                            ],
                            'links' => [
                                'self'
                            ]
                        ]
                    ]
                ]);
    }

    public function testUpdateUser()
    {
        $companyId = factory(\WA\DataStore\Company\Company::class)->create()->id;
        $addressId = factory(\WA\DataStore\Address\Address::class)->create()->id;

        $user1 = factory(\WA\DataStore\User\User::class)->create(['companyId' => $companyId, 'addressId' => $addressId]);
        $user2 = factory(\WA\DataStore\User\User::class)->create(['companyId' => $companyId, 'addressId' => $addressId]);
        
        $res = $this->json('PATCH', '/users/'.$user2->id,
            [
                'data' => [
                    'type' => 'users',
                    'attributes' => [
                        'uuid' => $user1->uuid,
                        'email' => $user1->email,
                        'alternateEmail' => $user1->alternateEmail,
                        'password' => $user1->password,
                        'username' => $user1->username,
                        'confirmation_code' => $user1->confirmation_code,
                        'remember_token' => $user1->remember_token,
                        'confirmed' => $user1->confirmed,
                        'firstName' => $user1->firstName,
                        'lastName' => $user1->lastName,
                        'alternateFirstName' => $user1->alternateFirstName,
                        'supervisorEmail' => $user1->supervisorEmail,
                        'companyUserIdentifier' => $user1->companyUserIdentifier,
                        'isSupervisor' => $user1->isSupervisor,
                        'isValidator' => $user1->isValidator,
                        'isActive' => $user1->isActive,
                        'rgt' => $user1->rgt,
                        'lft' => $user1->lft,
                        'hierarchy' => $user1->hierarchy,
                        'defaultLang' => $user1->defaultLang,
                        'notes' => $user1->notes,
                        'level' => $user1->level,
                        'notify' => $user1->notify,
                        'companyId' => $user1->companyId,
                        'syncId' => $user1->syncId,
                        'supervisorId' => $user1->supervisorId,
                        'externalId' => $user1->externalId,
                        'approverId' => $user1->approverId,
                        'defaultLocationId' => $user1->defaultLocationId,
                        'addressId' => $user1->addressId,
                    ]
                ]
            ]
            )
            //Log::debug("RES TEST: ".print_r($res->response->getContent(), true));
            ->seeJson(
                [
                    'uuid' => $user1->uuid,
                    'email' => $user1->email,
                    'alternateEmail' => $user1->alternateEmail,
                    'password' => $user1->password,
                    'username' => $user1->username,
                    'confirmation_code' => $user1->confirmation_code,
                    'remember_token' => $user1->remember_token,
                    'confirmed' => $user1->confirmed,
                    'firstName' => $user1->firstName,
                    'lastName' => $user1->lastName,
                    'alternateFirstName' => $user1->alternateFirstName,
                    'supervisorEmail' => $user1->supervisorEmail,
                    'companyUserIdentifier' => $user1->companyUserIdentifier,
                    'isSupervisor' => $user1->isSupervisor,
                    'isValidator' => $user1->isValidator,
                    'isActive' => $user1->isActive,
                    'rgt' => $user1->rgt,
                    'lft' => $user1->lft,
                    'hierarchy' => $user1->hierarchy,
                    'defaultLang' => $user1->defaultLang,
                    'notes' => $user1->notes,
                    'level' => $user1->level,
                    'notify' => $user1->notify,
                    'companyId' => $user1->companyId,
                    'syncId' => $user1->syncId,
                    'supervisorId' => $user1->supervisorId,
                    'externalId' => $user1->externalId,
                    'approverId' => $user1->approverId,
                    'defaultLocationId' => $user1->defaultLocationId,
                    'addressId' => $user1->addressId,
                ])
            ->seeJsonStructure(
                [
                    'data' => [
                        'type',
                        'id',
                        'attributes' => [
                            'uuid',
                            'email',
                            'alternateEmail',
                            'password',
                            'username',
                            'confirmation_code',
                            'remember_token',
                            'confirmed',
                            'firstName',
                            'lastName',
                            'alternateFirstName',
                            'supervisorEmail',
                            'companyUserIdentifier',
                            'isSupervisor',
                            'isValidator',
                            'isActive',
                            'rgt',
                            'lft',
                            'hierarchy',
                            'defaultLang',
                            'notes',
                            'level',
                            'notify',
                            'companyId',
                            'syncId',
                            'supervisorId',
                            'externalId',
                            'approverId',
                            'defaultLocationId',
                            'addressId',
                        ],
                        'links' => [
                            'self'
                        ],
                        'relationships' => [
                            'address' => [
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
                                'address',
                                'city',
                                'state',
                                'country',
                                'postalCode'
                            ],
                            'links' => [
                                'self'
                            ]
                        ]
                    ]
                ]);
    }

    public function testUpdateUserIncludeAllDeleteRelationships()
    {
        $companyId = factory(\WA\DataStore\Company\Company::class)->create()->id;
        $addressId = factory(\WA\DataStore\Address\Address::class)->create()->id;
        $user = factory(\WA\DataStore\User\User::class)->create(['companyId' => $companyId, 'addressId' => $addressId]);
 
        // ASSETS
        $asset1 = factory(\WA\DataStore\Asset\Asset::class)->create();
        $asset2 = factory(\WA\DataStore\Asset\Asset::class)->create();
        $arrayA = array($asset1->id, $asset2->id);
        $user->assets()->sync($arrayA);

        $userAssetDB = DB::table('user_assets')->where('userId', $user->id)->get();
        $this->assertCount(2, $userAssetDB);
        $this->assertEquals($userAssetDB[0]->assetId, $asset1->id);
        $this->assertEquals($userAssetDB[1]->assetId, $asset2->id);

        $asset1DB = DB::table('assets')->where('id', $asset1->id)->get()[0];
        $asset2DB = DB::table('assets')->where('id', $asset2->id)->get()[0];
        
        $this->assertEquals($asset1DB->id, $asset1->id);
        $this->assertEquals($asset1DB->identification, $asset1->identification);
        $this->assertEquals($asset1DB->active, $asset1->active);
        $this->assertEquals($asset1DB->externalId, $asset1->externalId);
        $this->assertEquals($asset1DB->typeId, $asset1->typeId);
        $this->assertEquals($asset1DB->carrierId, $asset1->carrierId);
        $this->assertEquals($asset1DB->statusId, $asset1->statusId);
        $this->assertEquals($asset1DB->syncId, $asset1->syncId);

        $this->assertEquals($asset2DB->id, $asset2->id);
        $this->assertEquals($asset2DB->identification, $asset2->identification);
        $this->assertEquals($asset2DB->active, $asset2->active);
        $this->assertEquals($asset2DB->externalId, $asset2->externalId);
        $this->assertEquals($asset2DB->typeId, $asset2->typeId);
        $this->assertEquals($asset2DB->carrierId, $asset2->carrierId);
        $this->assertEquals($asset2DB->statusId, $asset2->statusId);
        $this->assertEquals($asset2DB->syncId, $asset2->syncId);

        // DEVICES        
        $device1 = factory(\WA\DataStore\Device\Device::class)->create();
        $device2 = factory(\WA\DataStore\Device\Device::class)->create();
        $arrayD = array($device1->id, $device2->id);
        $user->devices()->sync($arrayD);

        $userDeviceDB = DB::table('user_devices')->where('userId', $user->id)->get();
        $this->assertCount(2, $userDeviceDB);
        $this->assertEquals($userDeviceDB[0]->deviceId, $device1->id);
        $this->assertEquals($userDeviceDB[1]->deviceId, $device2->id);

        $device1DB = DB::table('devices')->where('id', $device1->id)->get()[0];
        $device2DB = DB::table('devices')->where('id', $device2->id)->get()[0];
        
        $this->assertEquals($device1DB->id, $device1->id);
        $this->assertEquals($device1DB->identification, $device1->identification);
        $this->assertEquals($device1DB->name, $device1->name);
        $this->assertEquals($device1DB->properties, $device1->properties);
        $this->assertEquals($device1DB->externalId, $device1->externalId);
        $this->assertEquals($device1DB->deviceTypeId, $device1->deviceTypeId);
        $this->assertEquals($device1DB->statusId, $device1->statusId);
        $this->assertEquals($device1DB->syncId, $device1->syncId);

        $this->assertEquals($device2DB->id, $device2->id);
        $this->assertEquals($device2DB->identification, $device2->identification);
        $this->assertEquals($device2DB->name, $device2->name);
        $this->assertEquals($device2DB->properties, $device2->properties);
        $this->assertEquals($device2DB->externalId, $device2->externalId);
        $this->assertEquals($device2DB->deviceTypeId, $device2->deviceTypeId);
        $this->assertEquals($device2DB->statusId, $device2->statusId);
        $this->assertEquals($device2DB->syncId, $device2->syncId);

        // ROLES
        $role1 = factory(\WA\DataStore\Role\Role::class)->create();
        $role2 = factory(\WA\DataStore\Role\Role::class)->create();
        $arrayR = array($role1->id, $role2->id);
        $user->roles()->sync($arrayR);

        $userRoleDB = DB::table('role_user')->where('user_id', $user->id)->get();
        $this->assertCount(2, $userRoleDB);
        $this->assertEquals($userRoleDB[0]->role_id, $role1->id);
        $this->assertEquals($userRoleDB[1]->role_id, $role2->id);

        $role1DB = DB::table('roles')->where('id', $role1->id)->get()[0];
        $role2DB = DB::table('roles')->where('id', $role2->id)->get()[0];
        
        $this->assertEquals($role1DB->id, $role1->id);
        $this->assertEquals($role1DB->name, $role1->name);
        $this->assertEquals($role1DB->display_name, $role1->display_name);
        $this->assertEquals($role1DB->description, $role1->description);

        $this->assertEquals($role2DB->id, $role2->id);
        $this->assertEquals($role2DB->name, $role2->name);
        $this->assertEquals($role2DB->display_name, $role2->display_name);
        $this->assertEquals($role2DB->description, $role2->description);

        // UDLVALUES
        $udl1 = factory(\WA\DataStore\Udl\Udl::class)->create(['companyId' => $companyId]);
        $udl2 = factory(\WA\DataStore\Udl\Udl::class)->create(['companyId' => $companyId]);
        $udlV1 = factory(\WA\DataStore\UdlValue\UdlValue::class)->create(['udlId' => $udl1->id]);
        $udlV2 = factory(\WA\DataStore\UdlValue\UdlValue::class)->create(['udlId' => $udl2->id]);
        $arrayU = array($udlV1->id, $udlV2->id);
        $user->udlValues()->sync($arrayU);

        $userUdlDB = DB::table('employee_udls')->where('userId', $user->id)->get();
        $this->assertCount(2, $userUdlDB);
        $this->assertEquals($userUdlDB[0]->udlValueId, $udlV1->id);
        $this->assertEquals($userUdlDB[1]->udlValueId, $udlV2->id);

        $udlV1DB = DB::table('udl_values')->where('id', $udlV1->id)->get()[0];
        $udlV2DB = DB::table('udl_values')->where('id', $udlV2->id)->get()[0];
        
        $this->assertEquals($udlV1DB->id, $udlV1->id);
        $this->assertEquals($udlV1DB->name, $udlV1->name);
        $this->assertEquals($udlV1DB->udlId, $udlV1->udlId);
        $this->assertEquals($udlV1DB->externalId, $udlV1->externalId);

        $this->assertEquals($udlV2DB->id, $udlV2->id);
        $this->assertEquals($udlV2DB->name, $udlV2->name);
        $this->assertEquals($udlV2DB->udlId, $udlV2->udlId);
        $this->assertEquals($udlV2DB->externalId, $udlV2->externalId);

        // ALLOCATIONS
        $carrier = factory(\WA\DataStore\Carrier\Carrier::class)->create();
        $allocation1 = factory(\WA\DataStore\Allocation\Allocation::class)->create(['userId' => $user->id]);
        $allocation1->carriers()->associate($carrier);
        $allocation1->save();
        $allocation2 = factory(\WA\DataStore\Allocation\Allocation::class)->create(['userId' => $user->id]);
        $allocation2->carriers()->associate($carrier);
        $allocation2->save();

        $content1 = factory(\WA\DataStore\Content\Content::class)->create(['owner_id' => $user->id, 'owner_type' => 'users']);
        $content2 = factory(\WA\DataStore\Content\Content::class)->create(['owner_id' => $user->id, 'owner_type' => 'users']);

        $res = $this->json('PATCH', '/users/'.$user->id.'?include=assets,devices,roles,udls,allocations,companies,contents',
            [
                'data' => [
                    'type' => 'users',
                    'attributes' => [
                        'uuid' => $user->uuid,
                        'email' => $user->email,
                        'alternateEmail' => $user->alternateEmail,
                        'password' => $user->password,
                        'username' => $user->username,
                        'confirmation_code' => $user->confirmation_code,
                        'remember_token' => $user->remember_token,
                        'confirmed' => $user->confirmed,
                        'firstName' => $user->firstName,
                        'lastName' => $user->lastName,
                        'alternateFirstName' => $user->alternateFirstName,
                        'supervisorEmail' => $user->supervisorEmail,
                        'companyUserIdentifier' => $user->companyUserIdentifier,
                        'isSupervisor' => $user->isSupervisor,
                        'isValidator' => $user->isValidator,
                        'isActive' => $user->isActive,
                        'rgt' => $user->rgt,
                        'lft' => $user->lft,
                        'hierarchy' => $user->hierarchy,
                        'defaultLang' => $user->defaultLang,
                        'notes' => $user->notes,
                        'level' => $user->level,
                        'notify' => $user->notify,
                        'companyId' => $user->companyId,
                        'syncId' => $user->syncId,
                        'supervisorId' => $user->supervisorId,
                        'externalId' => $user->externalId,
                        'approverId' => $user->approverId,
                        'defaultLocationId' => $user->defaultLocationId,
                        'addressId' => $user->addressId,
                    ],
                    'relationships' => [
                        'assets' => [
                            'data' => [
                                ['type' => 'assets', 'id' => $asset1->id]
                            ],
                        ],
                        'devices' => [
                            'data' => [
                                ['type' => 'devices', 'id' => $device1->id]
                            ],
                        ],
                        'roles' => [
                            'data' => [
                                ['type' => 'roles', 'id' => $role1->id]
                            ],
                        ],
                        'udls' => [
                            'data' => [
                                ['type' => 'udls', 'id' => $udlV1->id]
                            ],
                        ],
                        'allocations' => [
                            'data' => [
                                [
                                    'id' => $allocation1->id,
                                    'billMonth' => $allocation1->billMonth,
                                    'mobileNumber' => $allocation1->mobileNumber,
                                    'carrier' => $allocation1->carrier,
                                    'currency' => $allocation1->currency,
                                    'handsetModel' => $allocation1->handsetModel,
                                    'totalAllocatedCharge' => $allocation1->totalAllocatedCharge,
                                    'preAllocatedAmountDue' => $allocation1->preAllocatedAmountDue,
                                    'otherAdjustments' => $allocation1->otherAdjustments,
                                    'preAdjustedAccessCharge' => $allocation1->preAdjustedAccessCharge,
                                    'adjustedAccessCost' => $allocation1->adjustedAccessCost,
                                    'bBCost' => $allocation1->bBCost,
                                    'pDACost' => $allocation1->pDACost,
                                    'iPhoneCost' => $allocation1->iPhoneCost,
                                    'featuresCost' => $allocation1->featuresCost,
                                    'dataCardCost' => $allocation1->dataCardCost,
                                    'lDCanadaCost' => $allocation1->lDCanadaCost,
                                    'uSAddOnPlanCost' => $allocation1->uSAddOnPlanCost,
                                    'uSLDAddOnPlanCost' => $allocation1->uSLDAddOnPlanCost,
                                    'uSDataRoamingCost' => $allocation1->uSDataRoamingCost,
                                    'nightAndWeekendAddOnCost' => $allocation1->nightAndWeekendAddOnCost,
                                    'minuteAddOnCost' => $allocation1->minuteAddOnCost,
                                    'servicePlanCharges' => $allocation1->servicePlanCharges,
                                    'directConnectCost' => $allocation1->directConnectCost,
                                    'textMessagingCost' => $allocation1->textMessagingCost,
                                    'dataCost' => $allocation1->dataCost,
                                    'intlRoamingCost' => $allocation1->intlRoamingCost,
                                    'intlLongDistanceCost' => $allocation1->intlLongDistanceCost,
                                    'directoryAssistanceCost' => $allocation1->directoryAssistanceCost,
                                    'callForwardingCost' => $allocation1->callForwardingCost,
                                    'airtimeCost' => $allocation1->airtimeCost,
                                    'usageCharges' => $allocation1->usageCharges,
                                    'equipmentCost' => $allocation1->equipmentCost,
                                    'otherDiscountChargesCost' => $allocation1->otherDiscountChargesCost,
                                    'taxes' => $allocation1->taxes,
                                    'thirdPartyCost' => $allocation1->thirdPartyCost,
                                    'otherCharges' => $allocation1->otherCharges,
                                    'waFees' => $allocation1->waFees,
                                    'lineFees' => $allocation1->lineFees,
                                    'mobilityFees' => $allocation1->mobilityFees,
                                    'fees' => $allocation1->fees,
                                    'last_upgrade' => $allocation1->last_upgrade
                                ]
                            ]
                        ],
                        'contents' => [
                            'data' => [
                                [
                                    'id' => $content1->id,
                                    'content' => $content1->content,
                                    'active' => $content1->active,
                                    'owner_type' => $content1->owner_type
                                ]
                            ]
                        ]
                    ],
                ],
            ]
            )
            //Log::debug("testUpdateUserIncludeAllDeleteRelationships: ".print_r($res->response->getContent(), true));
            ->seeJson(
                [
                    'uuid' => $user->uuid,
                    'email' => $user->email,
                    'alternateEmail' => $user->alternateEmail,
                    'password' => $user->password,
                    'username' => $user->username,
                    'confirmation_code' => $user->confirmation_code,
                    'remember_token' => $user->remember_token,
                    'confirmed' => $user->confirmed,
                    'firstName' => $user->firstName,
                    'lastName' => $user->lastName,
                    'alternateFirstName' => $user->alternateFirstName,
                    'supervisorEmail' => $user->supervisorEmail,
                    'companyUserIdentifier' => $user->companyUserIdentifier,
                    'isSupervisor' => $user->isSupervisor,
                    'isValidator' => $user->isValidator,
                    'isActive' => $user->isActive,
                    'rgt' => $user->rgt,
                    'lft' => $user->lft,
                    'hierarchy' => $user->hierarchy,
                    'defaultLang' => $user->defaultLang,
                    'notes' => $user->notes,
                    'level' => $user->level,
                    'notify' => $user->notify,
                    'companyId' => $user->companyId,
                    'syncId' => $user->syncId,
                    'supervisorId' => $user->supervisorId,
                    'externalId' => $user->externalId,
                    'approverId' => $user->approverId,
                    'defaultLocationId' => $user->defaultLocationId,
                    'addressId' => $user->addressId,
                ])
            ->seeJsonStructure(
                [
                    'data' => [
                        'type',
                        'id',
                        'attributes' => [
                            'uuid',
                            'email',
                            'alternateEmail',
                            'password',
                            'username',
                            'confirmation_code',
                            'remember_token',
                            'confirmed',
                            'firstName',
                            'lastName',
                            'alternateFirstName',
                            'supervisorEmail',
                            'companyUserIdentifier',
                            'isSupervisor',
                            'isValidator',
                            'isActive',
                            'rgt',
                            'lft',
                            'hierarchy',
                            'defaultLang',
                            'notes',
                            'level',
                            'notify',
                            'companyId',
                            'syncId',
                            'supervisorId',
                            'externalId',
                            'approverId',
                            'defaultLocationId',
                            'addressId',
                        ],
                        'links' => [
                            'self'
                        ],
                        'relationships' => [
                            'address' => [
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
                            ],
                            'assets' => [
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
                            ],
                            'devices' => [
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
                            ],
                            'companies' => [
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
                            ],
                            'roles' => [
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
                            ],
                            'allocations' => [
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
                            ],
                            'contents' => [
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
                            ],
                            'udls' => [
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
                                'make',
                                'model',
                                'class',
                                'deviceOS',
                                'description',
                                'statusId',
                                'image'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        1 => [
                            'type',
                            'id',
                            'attributes' => [
                                'address',
                                'city',
                                'state',
                                'country',
                                'postalCode'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        2 => [
                            'type',
                            'id',
                            'attributes' => [
                                'identification',
                                'active',
                                'statusId',
                                'typeId',
                                'externalId',
                                'carrierId',
                                'syncId'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        3 => [
                            'type',
                            'id',
                            'attributes' => [
                                'identification',
                                'name',
                                'properties',
                                'externalId',
                                'statusId',
                                'syncId'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        4 => [
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
                                'currentBillMonth'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        5 => [
                            'type',
                            'id',
                            'attributes' => [
                                'name'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        6 => [
                            'type',
                            'id',
                            'attributes' => [
                                'bill_month',
                                'carrier',
                                'mobile_number',
                                'currency',
                                'device',
                                'allocated_charge',
                                'service_plan_charge',
                                'usage_charge',
                                'other_charge',
                                'fees',
                                'last_upgrade'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        7 => [
                            'type',
                            'id',
                            'attributes' => [
                                'content',
                                'active',
                                'owner_type',
                                'owner_id'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        8 => [
                            'type',
                            'id',
                            'attributes' => [
                                'udlId',
                                'udlValue'
                            ],
                            'links' => [
                                'self'
                            ]
                        ]
                    ]
                ]);
    }

    public function testUpdateUserIncludeAllAddRelationships()
    {
        $companyId = factory(\WA\DataStore\Company\Company::class)->create()->id;
        $addressId = factory(\WA\DataStore\Address\Address::class)->create()->id;
        $user = factory(\WA\DataStore\User\User::class)->create(['companyId' => $companyId, 'addressId' => $addressId]);
 
        $asset1 = factory(\WA\DataStore\Asset\Asset::class)->create()->id;
        $asset2 = factory(\WA\DataStore\Asset\Asset::class)->create()->id;
        $arrayA = array($asset1, $asset2);
        $user->assets()->sync($arrayA);
        $asset3 = factory(\WA\DataStore\Asset\Asset::class)->create()->id;

        $device1 = factory(\WA\DataStore\Device\Device::class)->create()->id;
        $device2 = factory(\WA\DataStore\Device\Device::class)->create()->id;
        $arrayD = array($device1, $device2);
        $user->devices()->sync($arrayD);
        $device3 = factory(\WA\DataStore\Device\Device::class)->create()->id;

        $role1 = factory(\WA\DataStore\Role\Role::class)->create()->id;
        $role2 = factory(\WA\DataStore\Role\Role::class)->create()->id;
        $arrayR = array($role1, $role2);
        $user->roles()->sync($arrayR);
        $role3 = factory(\WA\DataStore\Role\Role::class)->create()->id;

        $udl1 = factory(\WA\DataStore\Udl\Udl::class)->create(['companyId' => $companyId])->id;
        $udl2 = factory(\WA\DataStore\Udl\Udl::class)->create(['companyId' => $companyId])->id;
        $udlV1 = factory(\WA\DataStore\UdlValue\UdlValue::class)->create(['udlId' => $udl1])->id;
        $udlV2 = factory(\WA\DataStore\UdlValue\UdlValue::class)->create(['udlId' => $udl2])->id;
        $arrayU = array($udlV1, $udlV2);
        $user->udlValues()->sync($arrayU);
        $udl3 = factory(\WA\DataStore\Udl\Udl::class)->create(['companyId' => $companyId])->id;
        $udlV3 = factory(\WA\DataStore\UdlValue\UdlValue::class)->create(['udlId' => $udl2])->id;

        $carrier = factory(\WA\DataStore\Carrier\Carrier::class)->create();
        $allocation1 = factory(\WA\DataStore\Allocation\Allocation::class)->create(['userId' => $user->id]);
        $allocation1->carriers()->associate($carrier);
        $allocation1->save();
        $allocation2 = factory(\WA\DataStore\Allocation\Allocation::class)->create(['userId' => $user->id]);
        $allocation2->carriers()->associate($carrier);
        $allocation2->save();
        
        $content1 = factory(\WA\DataStore\Content\Content::class)->create(['owner_id' => $user->id, 'owner_type' => 'users']);
        $content2 = factory(\WA\DataStore\Content\Content::class)->create(['owner_id' => $user->id, 'owner_type' => 'users']);
        
        $res = $this->json('PATCH', '/users/'.$user->id.'?include=assets,devices,roles,udls,allocations,companies,contents',
            [
                'data' => [
                    'type' => 'users',
                    'attributes' => [
                        'uuid' => $user->uuid,
                        'email' => $user->email,
                        'alternateEmail' => $user->alternateEmail,
                        'password' => $user->password,
                        'username' => $user->username,
                        'confirmation_code' => $user->confirmation_code,
                        'remember_token' => $user->remember_token,
                        'confirmed' => $user->confirmed,
                        'firstName' => $user->firstName,
                        'lastName' => $user->lastName,
                        'alternateFirstName' => $user->alternateFirstName,
                        'supervisorEmail' => $user->supervisorEmail,
                        'companyUserIdentifier' => $user->companyUserIdentifier,
                        'isSupervisor' => $user->isSupervisor,
                        'isValidator' => $user->isValidator,
                        'isActive' => $user->isActive,
                        'rgt' => $user->rgt,
                        'lft' => $user->lft,
                        'hierarchy' => $user->hierarchy,
                        'defaultLang' => $user->defaultLang,
                        'notes' => $user->notes,
                        'level' => $user->level,
                        'notify' => $user->notify,
                        'companyId' => $user->companyId,
                        'syncId' => $user->syncId,
                        'supervisorId' => $user->supervisorId,
                        'externalId' => $user->externalId,
                        'approverId' => $user->approverId,
                        'defaultLocationId' => $user->defaultLocationId,
                        'addressId' => $user->addressId,
                    ],
                    'relationships' => [
                        'assets' => [
                            'data' => [
                                ['type' => 'assets', 'id' => $asset1],
                                ['type' => 'assets', 'id' => $asset2],
                                ['type' => 'assets', 'id' => $asset3]
                            ],
                        ],
                        'devices' => [
                            'data' => [
                                ['type' => 'devices', 'id' => $device1],
                                ['type' => 'devices', 'id' => $device2],
                                ['type' => 'devices', 'id' => $device3]
                            ],
                        ],
                        'roles' => [
                            'data' => [
                                ['type' => 'roles', 'id' => $role1],
                                ['type' => 'roles', 'id' => $role2],
                                ['type' => 'roles', 'id' => $role3]
                            ],
                        ],
                        'udls' => [
                            'data' => [
                                ['type' => 'udls', 'id' => $udlV1],
                                ['type' => 'udls', 'id' => $udlV2],
                                ['type' => 'udls', 'id' => $udlV3]
                            ],
                        ],
                        'allocations' => [
                            'data' => [
                                [
                                    'id' => 1,
                                    'billMonth' => $allocation1->billMonth,
                                    'mobileNumber' => $allocation1->mobileNumber,
                                    'carrier' => $allocation1->carrier,
                                    'currency' => $allocation1->currency,
                                    'handsetModel' => $allocation1->handsetModel,
                                    'totalAllocatedCharge' => $allocation1->totalAllocatedCharge,
                                    'preAllocatedAmountDue' => $allocation1->preAllocatedAmountDue,
                                    'otherAdjustments' => $allocation1->otherAdjustments,
                                    'preAdjustedAccessCharge' => $allocation1->preAdjustedAccessCharge,
                                    'adjustedAccessCost' => $allocation1->adjustedAccessCost,
                                    'bBCost' => $allocation1->bBCost,
                                    'pDACost' => $allocation1->pDACost,
                                    'iPhoneCost' => $allocation1->iPhoneCost,
                                    'featuresCost' => $allocation1->featuresCost,
                                    'dataCardCost' => $allocation1->dataCardCost,
                                    'lDCanadaCost' => $allocation1->lDCanadaCost,
                                    'uSAddOnPlanCost' => $allocation1->uSAddOnPlanCost,
                                    'uSLDAddOnPlanCost' => $allocation1->uSLDAddOnPlanCost,
                                    'uSDataRoamingCost' => $allocation1->uSDataRoamingCost,
                                    'nightAndWeekendAddOnCost' => $allocation1->nightAndWeekendAddOnCost,
                                    'minuteAddOnCost' => $allocation1->minuteAddOnCost,
                                    'servicePlanCharges' => $allocation1->servicePlanCharges,
                                    'directConnectCost' => $allocation1->directConnectCost,
                                    'textMessagingCost' => $allocation1->textMessagingCost,
                                    'dataCost' => $allocation1->dataCost,
                                    'intlRoamingCost' => $allocation1->intlRoamingCost,
                                    'intlLongDistanceCost' => $allocation1->intlLongDistanceCost,
                                    'directoryAssistanceCost' => $allocation1->directoryAssistanceCost,
                                    'callForwardingCost' => $allocation1->callForwardingCost,
                                    'airtimeCost' => $allocation1->airtimeCost,
                                    'usageCharges' => $allocation1->usageCharges,
                                    'equipmentCost' => $allocation1->equipmentCost,
                                    'otherDiscountChargesCost' => $allocation1->otherDiscountChargesCost,
                                    'taxes' => $allocation1->taxes,
                                    'thirdPartyCost' => $allocation1->thirdPartyCost,
                                    'otherCharges' => $allocation1->otherCharges,
                                    'waFees' => $allocation1->waFees,
                                    'lineFees' => $allocation1->lineFees,
                                    'mobilityFees' => $allocation1->mobilityFees,
                                    'fees' => $allocation1->fees,
                                    'last_upgrade' => $allocation1->last_upgrade
                                ],
                                [
                                    'id' => 2,
                                    'billMonth' => $allocation2->billMonth,
                                    'mobileNumber' => $allocation2->mobileNumber,
                                    'carrier' => $allocation2->carrier,
                                    'currency' => $allocation2->currency,
                                    'handsetModel' => $allocation2->handsetModel,
                                    'totalAllocatedCharge' => $allocation2->totalAllocatedCharge,
                                    'preAllocatedAmountDue' => $allocation2->preAllocatedAmountDue,
                                    'otherAdjustments' => $allocation2->otherAdjustments,
                                    'preAdjustedAccessCharge' => $allocation2->preAdjustedAccessCharge,
                                    'adjustedAccessCost' => $allocation2->adjustedAccessCost,
                                    'bBCost' => $allocation2->bBCost,
                                    'pDACost' => $allocation2->pDACost,
                                    'iPhoneCost' => $allocation2->iPhoneCost,
                                    'featuresCost' => $allocation2->featuresCost,
                                    'dataCardCost' => $allocation2->dataCardCost,
                                    'lDCanadaCost' => $allocation2->lDCanadaCost,
                                    'uSAddOnPlanCost' => $allocation2->uSAddOnPlanCost,
                                    'uSLDAddOnPlanCost' => $allocation2->uSLDAddOnPlanCost,
                                    'uSDataRoamingCost' => $allocation2->uSDataRoamingCost,
                                    'nightAndWeekendAddOnCost' => $allocation2->nightAndWeekendAddOnCost,
                                    'minuteAddOnCost' => $allocation2->minuteAddOnCost,
                                    'servicePlanCharges' => $allocation2->servicePlanCharges,
                                    'directConnectCost' => $allocation2->directConnectCost,
                                    'textMessagingCost' => $allocation2->textMessagingCost,
                                    'dataCost' => $allocation2->dataCost,
                                    'intlRoamingCost' => $allocation2->intlRoamingCost,
                                    'intlLongDistanceCost' => $allocation2->intlLongDistanceCost,
                                    'directoryAssistanceCost' => $allocation2->directoryAssistanceCost,
                                    'callForwardingCost' => $allocation2->callForwardingCost,
                                    'airtimeCost' => $allocation2->airtimeCost,
                                    'usageCharges' => $allocation2->usageCharges,
                                    'equipmentCost' => $allocation2->equipmentCost,
                                    'otherDiscountChargesCost' => $allocation2->otherDiscountChargesCost,
                                    'taxes' => $allocation2->taxes,
                                    'thirdPartyCost' => $allocation2->thirdPartyCost,
                                    'otherCharges' => $allocation2->otherCharges,
                                    'waFees' => $allocation2->waFees,
                                    'lineFees' => $allocation2->lineFees,
                                    'mobilityFees' => $allocation2->mobilityFees,
                                    'fees' => $allocation2->fees,
                                    'last_upgrade' => $allocation2->last_upgrade
                                ],
                                [
                                    'id' => 0,
                                    'billMonth' => $allocation1->billMonth,
                                    'mobileNumber' => $allocation1->mobileNumber,
                                    'carrier' => $allocation1->carrier,
                                    'currency' => $allocation1->currency,
                                    'handsetModel' => $allocation1->handsetModel,
                                    'totalAllocatedCharge' => $allocation1->totalAllocatedCharge,
                                    'preAllocatedAmountDue' => $allocation1->preAllocatedAmountDue,
                                    'otherAdjustments' => $allocation1->otherAdjustments,
                                    'preAdjustedAccessCharge' => $allocation1->preAdjustedAccessCharge,
                                    'adjustedAccessCost' => $allocation1->adjustedAccessCost,
                                    'bBCost' => $allocation1->bBCost,
                                    'pDACost' => $allocation1->pDACost,
                                    'iPhoneCost' => $allocation1->iPhoneCost,
                                    'featuresCost' => $allocation1->featuresCost,
                                    'dataCardCost' => $allocation1->dataCardCost,
                                    'lDCanadaCost' => $allocation1->lDCanadaCost,
                                    'uSAddOnPlanCost' => $allocation1->uSAddOnPlanCost,
                                    'uSLDAddOnPlanCost' => $allocation1->uSLDAddOnPlanCost,
                                    'uSDataRoamingCost' => $allocation1->uSDataRoamingCost,
                                    'nightAndWeekendAddOnCost' => $allocation1->nightAndWeekendAddOnCost,
                                    'minuteAddOnCost' => $allocation1->minuteAddOnCost,
                                    'servicePlanCharges' => $allocation1->servicePlanCharges,
                                    'directConnectCost' => $allocation2->directConnectCost,
                                    'textMessagingCost' => $allocation2->textMessagingCost,
                                    'dataCost' => $allocation2->dataCost,
                                    'intlRoamingCost' => $allocation2->intlRoamingCost,
                                    'intlLongDistanceCost' => $allocation2->intlLongDistanceCost,
                                    'directoryAssistanceCost' => $allocation2->directoryAssistanceCost,
                                    'callForwardingCost' => $allocation2->callForwardingCost,
                                    'airtimeCost' => $allocation2->airtimeCost,
                                    'usageCharges' => $allocation2->usageCharges,
                                    'equipmentCost' => $allocation2->equipmentCost,
                                    'otherDiscountChargesCost' => $allocation2->otherDiscountChargesCost,
                                    'taxes' => $allocation2->taxes,
                                    'thirdPartyCost' => $allocation2->thirdPartyCost,
                                    'otherCharges' => $allocation2->otherCharges,
                                    'waFees' => $allocation2->waFees,
                                    'lineFees' => $allocation2->lineFees,
                                    'mobilityFees' => $allocation2->mobilityFees,
                                    'fees' => $allocation2->fees,
                                    'last_upgrade' => $allocation2->last_upgrade
                                ]
                            ]
                        ],
                        'contents' => [
                            'data' => [
                                [
                                    'id' => 1,
                                    'content' => $content1->content,
                                    'active' => $content1->active,
                                    'owner_type' => $content1->owner_type
                                ],
                                [
                                    'id' => 2,
                                    'content' => $content2->content,
                                    'active' => $content2->active,
                                    'owner_type' => $content2->owner_type
                                ],
                                [
                                    'id' => 0,
                                    'content' => $content2->content,
                                    'active' => $content1->active,
                                    'owner_type' => $content2->owner_type
                                ]
                            ]
                        ]
                    ],
                ],
            ]
            )
            //Log::debug("testUpdateUserIncludeAllAddRelationships: ".print_r($res->response->getContent(), true));
            ->seeJson(
                [
                    'uuid' => $user->uuid,
                    'email' => $user->email,
                    'alternateEmail' => $user->alternateEmail,
                    'password' => $user->password,
                    'username' => $user->username,
                    'confirmation_code' => $user->confirmation_code,
                    'remember_token' => $user->remember_token,
                    'confirmed' => $user->confirmed,
                    'firstName' => $user->firstName,
                    'lastName' => $user->lastName,
                    'alternateFirstName' => $user->alternateFirstName,
                    'supervisorEmail' => $user->supervisorEmail,
                    'companyUserIdentifier' => $user->companyUserIdentifier,
                    'isSupervisor' => $user->isSupervisor,
                    'isValidator' => $user->isValidator,
                    'isActive' => $user->isActive,
                    'rgt' => $user->rgt,
                    'lft' => $user->lft,
                    'hierarchy' => $user->hierarchy,
                    'defaultLang' => $user->defaultLang,
                    'notes' => $user->notes,
                    'level' => $user->level,
                    'notify' => $user->notify,
                    'companyId' => $user->companyId,
                    'syncId' => $user->syncId,
                    'supervisorId' => $user->supervisorId,
                    'externalId' => $user->externalId,
                    'approverId' => $user->approverId,
                    'defaultLocationId' => $user->defaultLocationId,
                    'addressId' => $user->addressId,
                ])
            ->seeJsonStructure(
                [
                    'data' => [
                        'type',
                        'id',
                        'attributes' => [
                            'uuid',
                            'email',
                            'alternateEmail',
                            'password',
                            'username',
                            'confirmation_code',
                            'remember_token',
                            'confirmed',
                            'firstName',
                            'lastName',
                            'alternateFirstName',
                            'supervisorEmail',
                            'companyUserIdentifier',
                            'isSupervisor',
                            'isValidator',
                            'isActive',
                            'rgt',
                            'lft',
                            'hierarchy',
                            'defaultLang',
                            'notes',
                            'level',
                            'notify',
                            'companyId',
                            'syncId',
                            'supervisorId',
                            'externalId',
                            'approverId',
                            'defaultLocationId',
                            'addressId',
                        ],
                        'links' => [
                            'self'
                        ],
                        'relationships' => [
                            'address' => [
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
                            ],
                            'assets' => [
                                'links' => [
                                    'self',
                                    'related'
                                ],
                                'data' => [
                                    0 => [
                                        'type',
                                        'id'
                                    ],
                                    1 => [
                                        'type',
                                        'id'
                                    ],
                                    2 => [
                                        'type',
                                        'id'
                                    ]
                                ]
                            ],
                            'devices' => [
                                'links' => [
                                    'self',
                                    'related'
                                ],
                                'data' => [
                                    0 => [
                                        'type',
                                        'id'
                                    ],
                                    1 => [
                                        'type',
                                        'id'
                                    ],
                                    2 => [
                                        'type',
                                        'id'
                                    ]
                                ]
                            ],
                            'companies' => [
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
                            ],
                            'roles' => [
                                'links' => [
                                    'self',
                                    'related'
                                ],
                                'data' => [
                                    0 => [
                                        'type',
                                        'id'
                                    ],
                                    1 => [
                                        'type',
                                        'id'
                                    ],
                                    2 => [
                                        'type',
                                        'id'
                                    ]
                                ]
                            ],
                            'allocations' => [
                                'links' => [
                                    'self',
                                    'related'
                                ],
                                'data' => [
                                    0 => [
                                        'type',
                                        'id'
                                    ],
                                    1 => [
                                        'type',
                                        'id'
                                    ],
                                    2 => [
                                        'type',
                                        'id'
                                    ]
                                ]
                            ],
                            'contents' => [
                                'links' => [
                                    'self',
                                    'related'
                                ],
                                'data' => [
                                    0 => [
                                        'type',
                                        'id'
                                    ],
                                    1 => [
                                        'type',
                                        'id'
                                    ],
                                    2 => [
                                        'type',
                                        'id'
                                    ]
                                ]
                            ],
                            'udls' => [
                                'links' => [
                                    'self',
                                    'related'
                                ],
                                'data' => [
                                    0 => [
                                        'type',
                                        'id'
                                    ],
                                    1 => [
                                        'type',
                                        'id'
                                    ],
                                    2 => [
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
                                'make',
                                'model',
                                'class',
                                'deviceOS',
                                'description',
                                'statusId',
                                'image'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        1 => [
                            'type',
                            'id',
                            'attributes' => [
                                'make',
                                'model',
                                'class',
                                'deviceOS',
                                'description',
                                'statusId',
                                'image'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        2 => [
                            'type',
                            'id',
                            'attributes' => [
                                'make',
                                'model',
                                'class',
                                'deviceOS',
                                'description',
                                'statusId',
                                'image'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        3 => [
                            'type',
                            'id',
                            'attributes' => [
                                'address',
                                'city',
                                'state',
                                'country',
                                'postalCode'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        4 => [
                            'type',
                            'id',
                            'attributes' => [
                                'identification',
                                'active',
                                'statusId',
                                'typeId',
                                'externalId',
                                'carrierId',
                                'syncId'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        5 => [
                            'type',
                            'id',
                            'attributes' => [
                                'identification',
                                'active',
                                'statusId',
                                'typeId',
                                'externalId',
                                'carrierId',
                                'syncId'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        6 => [
                            'type',
                            'id',
                            'attributes' => [
                                'identification',
                                'active',
                                'statusId',
                                'typeId',
                                'externalId',
                                'carrierId',
                                'syncId'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        7 => [
                            'type',
                            'id',
                            'attributes' => [
                                'identification',
                                'name',
                                'properties',
                                'externalId',
                                'statusId',
                                'syncId'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        8 => [
                            'type',
                            'id',
                            'attributes' => [
                                'identification',
                                'name',
                                'properties',
                                'externalId',
                                'statusId',
                                'syncId'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        9 => [
                            'type',
                            'id',
                            'attributes' => [
                                'identification',
                                'name',
                                'properties',
                                'externalId',
                                'statusId',
                                'syncId'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        10 => [
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
                                'currentBillMonth'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        11 => [
                            'type',
                            'id',
                            'attributes' => [
                                'name'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        12 => [
                            'type',
                            'id',
                            'attributes' => [
                                'name'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        13 => [
                            'type',
                            'id',
                            'attributes' => [
                                'name'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        14 => [
                            'type',
                            'id',
                            'attributes' => [
                                'bill_month',
                                'carrier',
                                'mobile_number',
                                'currency',
                                'device',
                                'allocated_charge',
                                'service_plan_charge',
                                'usage_charge',
                                'other_charge',
                                'fees',
                                'last_upgrade'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        15 => [
                            'type',
                            'id',
                            'attributes' => [
                                'bill_month',
                                'carrier',
                                'mobile_number',
                                'currency',
                                'device',
                                'allocated_charge',
                                'service_plan_charge',
                                'usage_charge',
                                'other_charge',
                                'fees',
                                'last_upgrade'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        16 => [
                            'type',
                            'id',
                            'attributes' => [
                                'bill_month',
                                'carrier',
                                'mobile_number',
                                'currency',
                                'device',
                                'allocated_charge',
                                'service_plan_charge',
                                'usage_charge',
                                'other_charge',
                                'fees',
                                'last_upgrade'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        17 => [
                            'type',
                            'id',
                            'attributes' => [
                                'content',
                                'active',
                                'owner_type',
                                'owner_id'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        18 => [
                            'type',
                            'id',
                            'attributes' => [
                                'content',
                                'active',
                                'owner_type',
                                'owner_id'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        19 => [
                            'type',
                            'id',
                            'attributes' => [
                                'content',
                                'active',
                                'owner_type',
                                'owner_id'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        20 => [
                            'type',
                            'id',
                            'attributes' => [
                                'udlId',
                                'udlValue'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        21 => [
                            'type',
                            'id',
                            'attributes' => [
                                'udlId',
                                'udlValue'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        22 => [
                            'type',
                            'id',
                            'attributes' => [
                                'udlId',
                                'udlValue'
                            ],
                            'links' => [
                                'self'
                            ]
                        ]
                    ]
                ]);
    }

    public function testDeleteUserIfExists()
    {
        // CREATE & DELETE
        $user = factory(\WA\DataStore\User\User::class)->create();
        $responseDel = $this->call('DELETE', '/users/'.$user->id);
        $this->assertEquals(200, $responseDel->status());
        $responseGet = $this->call('GET', '/users/'.$user->id);
        $this->assertEquals(404, $responseGet->status());
    }

    public function testDeleteUserIfNoExists()
    {
        // DELETE NO EXISTING.
        $responseDel = $this->call('DELETE', '/users/1');
        $this->assertEquals(404, $responseDel->status());
    }*/
}