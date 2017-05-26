<?php

/**
 * RoleUsersTableSeeder - Insert info into database.
 *
 * @author   Marcio de Rezende
 */
class RoleUsersTableSeeder extends BaseTableSeeder
{
    protected $table = 'role_user';

    /**
     * Run the database seeds.
     */
    public function run()
    {
        $this->deleteTable();

        $data = [

            [
                'user_id' => 1,
                'role_id' => 1,               
            ],
            [
                'user_id' => 6,
                'role_id' => 2,               
            ],
            [
                'user_id' => 7,
                'role_id' => 3,               
            ],
            [
                'user_id' => 8,
                'role_id' => 3,               
            ]           
        ];

        $this->loadTable($data);
    }
}
