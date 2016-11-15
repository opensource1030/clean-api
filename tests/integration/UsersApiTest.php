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
         $user = factory(\WA\DataStore\User\User::class)->create();

         $this->get('/users')
            ->seeJsonStructure([
                'data' => [
                    0 => ['type', 'id',
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
                        'links',
                    ],

                ],

            ]);
     }

    public function testGetUserById()
    {
        $user = factory(\WA\DataStore\User\User::class)->create();

        $this->get('/users/'.$user->id)
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
                    'links',
                ],
            ]);
    }
}
