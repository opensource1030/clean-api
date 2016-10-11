<?php

/**
 * PackageServicesTableSeeder - Insert info into database.
 *  
 * @author   Agustí Dosaiguas
 */

class PackageServicesTableSeeder extends BaseTableSeeder
{
    protected $table = "package_services";

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
                'packageId'     => 1,
                'servicesId'      => 1
            ],
            [
                'packageId'     => 1,
                'servicesId'      => 2
            ],
            [
                'packageId'     => 1,
                'servicesId'      => 5
            ],
            [
                'packageId'     => 1,
                'servicesId'      => 11
            ]
        ];

        $this->loadTable($data);
    }
}