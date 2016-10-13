<?php

/**
 * PackageAppsTableSeeder - Insert info into database.
 *  
 * @author   Agustí Dosaiguas
 */

class PackageAppsTableSeeder extends BaseTableSeeder
{
    protected $table = "package_apps";

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
                'appsId'      => 1
            ],
            [
                'packageId'     => 1,
                'appsId'      => 2
            ],
            [
                'packageId'     => 1,
                'appsId'      => 3
            ],
            [
                'packageId'     => 1,
                'appsId'      => 4
            ]
        ];

        $this->loadTable($data);
    }
}