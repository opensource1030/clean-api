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
                'name'     => "Package1",
                'conditionsId'      => 1,
                'devicesId'      => 2,
                'appsId'      => 1,
                'servicesId'      => 3
            ],
            [
                'name'     => "Package2",
                'conditionsId'      => 1,
                'devicesId'      => 2,
                'appsId'      => 4,
                'servicesId'      => 2
            ],
            [
                'name'     => "Package3",
                'conditionsId'      => 1,
                'devicesId'      => 3,
                'appsId'      => 1,
                'servicesId'      => 1
            ],
            [
                'name'     => "Package4",
                'conditionsId'      => 3,
                'devicesId'      => 4,
                'appsId'      => 1,
                'servicesId'      => 2
            ],
        ];

        $this->loadTable($data);
    }
}