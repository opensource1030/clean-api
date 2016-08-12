<?php


class UsersTableSeeder extends BaseTableSeeder
{
    protected $table = 'users';

    public function run()
    {
        $this->deleteTable();


        factory(\WA\DataStore\User\User::class, 40)->create();
    }

}
