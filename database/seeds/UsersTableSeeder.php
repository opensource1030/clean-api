<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{

    public function run()
    {
        factory(\WA\DataStore\User\User::class, 40)->create();
    }

}
