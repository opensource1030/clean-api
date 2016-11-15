<?php

class UserRolesTableSeeder extends BaseTableSeeder
{
    protected $table = 'role_user';

    public function run()
    {
        $this->deleteTable();

        $data = [
            [
                'user_id' => 1,
                'role_id' => 5
            ],
            [
                'user_id' => 1,
                'role_id' => 12
            ],
            [
                'user_id' => 1,
                'role_id' => 27
            ],
            [
                'user_id' => 1,
                'role_id' => 39
            ],
            [
                'user_id' => 2,
                'role_id' => 5
            ],
            [
                'user_id' => 2,
                'role_id' => 12
            ],
            [
                'user_id' => 2,
                'role_id' => 27
            ],
            [
                'user_id' => 2,
                'role_id' => 39
            ],
            [
                'user_id' => 3,
                'role_id' => 5
            ],
            [
                'user_id' => 3,
                'role_id' => 12
            ],
            [
                'user_id' => 3,
                'role_id' => 27
            ],
            [
                'user_id' => 3,
                'role_id' => 39
            ],
        ];

        $this->loadTable($data);
    }
}
