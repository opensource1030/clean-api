<?php

use WA\Testing\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;


class UsersTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * A basic functional test for user endpoints
     *
     *
     */
    public function testGetUsers()
    {
        $user = factory(\WA\DataStore\User\User::class)->create();        

        $this->get('/api/users')
            ->seeJsonStructure([
                'data' => [
                    0 => [ 'type','id',
                        'attributes' => [
                            'identification', 'email', 'username', 'supervisor_email', 'first_name', 'last_name'
                        ],
                        'links'
                    ]

                ]

            ]);

    }

    public function testGetUserById()
    {
        $user = factory(\WA\DataStore\User\User::class)->create();

        $this->get('/api/users/'.$user->id)
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

    public function testRelationshipWithPages()
    {
        $user = factory(\WA\DataStore\User\User::class)->create();

        $page = factory(\WA\DataStore\Page\Page::class)->create();

        $this->put('/api/pages/'.$page->id, [
            "title" => $page->title,
            'section' => $page->section,
            'content' => $page->content,
            'active' => $page->active,
            'owner_type' => 'user',
            'owner_id' => $user->id,
        ]);

        $this->get('/api/users/'.$user->id.'?include=pages')
            ->seeJson([
                'type' => 'pages',
                'id' => "$page->id",
                "title" => $page->title,
                'section' => $page->section,
                'content' => $page->content,
                'active' => $page->active,
                'owner_type' => 'user',
                'owner_id' => $user->id,

            ]);

    }

}