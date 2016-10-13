<?php

/**
 * PackagesTableSeeder - Insert info into database.
 *  
 * @author   Agustí Dosaiguas
 */

class PackagesTableSeeder extends BaseTableSeeder
{
    protected $table = "packages";

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->deleteTable();

        $data = [

            [
                'name'     => "Package1"
            ]
        ];

        $this->loadTable($data);
    }
}