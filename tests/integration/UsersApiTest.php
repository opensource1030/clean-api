<?php

use Laravel\Lumen\Testing\DatabaseMigrations;

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

    public function testGetUserByIdandIncludesAssets()
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
                            'udlName',
                            'udlLabel',
                            'companyName',
                            'companyLabel',
                            'companySName'
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

        $allocation1 = factory(\WA\DataStore\Allocation\Allocation::class)->create(['userId' => $user->id])->id;
        $allocation2 = factory(\WA\DataStore\Allocation\Allocation::class)->create(['userId' => $user->id])->id;

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
                    ],

                ],
            ]);
    }

    public function testGetUserByIdandIncludesContents()
    {
        $user = factory(\WA\DataStore\User\User::class)->create();

        $content1 = factory(\WA\DataStore\Content\Content::class)->create(['owner_id' => $user->id])->id;
        $content2 = factory(\WA\DataStore\Content\Content::class)->create(['owner_id' => $user->id])->id;

        $res = $this->json('GET', 'users/'.$user->id.'?include=contents');
        //Log::debug("Users/id: ".print_r($res->response->getContent(), true));
        /*    ->seeJsonStructure([
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
            ]);*/
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
        $allocation2 = factory(\WA\DataStore\Allocation\Allocation::class)->create();

        $content1 = factory(\WA\DataStore\Content\Content::class)->create();
        $content2 = factory(\WA\DataStore\Content\Content::class)->create();

        $res = $this->json('POST', '/users?include=assets,devices,roles,udls,allocations,companies',
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
                                'udlName',
                                'udlLabel',
                                'companyName',
                                'companyLabel',
                                'companySName'
                            ],
                            'links' => [
                                'self'
                            ]
                        ],
                        13 => [
                            'type',
                            'id',
                            'attributes' => [
                                'udlName',
                                'udlLabel',
                                'companyName',
                                'companyLabel',
                                'companySName'
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
        
        $res = $this->json('PUT', '/users/'.$user2->id,
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
            //Log::debug("RES: ".print_r($res->response->getContent(), true));
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
    }
}