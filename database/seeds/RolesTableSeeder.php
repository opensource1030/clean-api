<?php

/*
 * RolesTableSeeder - Insert info into database.
 *
 * @author   Agustí Dosaiguas
 */
class RolesTableSeeder extends BaseTableSeeder
{
    protected $table = 'roles';

    /**
     * Run the database seeds.
     */
    public function run()
    {
        $this->deleteTable();

        $data = [
            [
                'name' => 'admin',
                'display_name' => 'admin',
                'description' => 'User with full power and access',
            ],
            [
                'name' => 'wta',
                'display_name' => 'wta',
                'description' => 'User with full power and access of his own company information',
            ],
            [
                'name' => 'user',
                'display_name' => 'user',
                'description' => 'Normal user that can only retrieve information and create/update Orders',
            ]
        ];

        $this->loadTable($data);
    }
}
