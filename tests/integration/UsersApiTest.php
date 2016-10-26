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
                            'identification', 'email', 'username', 'supervisor_email', 'first_name', 'last_name',
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
            ->seeJson([
                'type' => 'users',
                'id' => "$user->id",
                'identification' => $user->identification,
                'email' => $user->email,
                'username' => $user->username,
                'first_name' => $user->firstName,
                'last_name' => $user->lastName,
            ]);
    }
}
