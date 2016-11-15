<?php

/**
 * PricesTableSeeder - Insert info into database.
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
                'name' => "Game Master",
                'display_name' => null,
                'description' => null
            ],
            [
                'name' => "Gamer",
                'display_name' => null,
                'description' => null
            ],
            [
                'name' => "Cheater",
                'display_name' => null,
                'description' => null
            ],
            [
                'name' => "User",
                'display_name' => null,
                'description' => null
            ],
            [
                'name' => "Noob",
                'display_name' => null,
                'description' => null
            ]
        ];
        
        $this->loadTable($data);
    }
}
