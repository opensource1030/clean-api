<?php

/**
 * PackageDevicesTableSeeder - Insert info into database.
 *  
 * @author   Agustí Dosaiguas
 */

class PackageDevicesTableSeeder extends BaseTableSeeder
{
    protected $table = "package_devices";

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
                'deviceId'      => 1
            ],
            [
                'packageId'     => 1,
                'deviceId'      => 2
            ],
            [
                'packageId'     => 1,
                'deviceId'      => 3
            ],
            [
                'packageId'     => 1,
                'deviceId'      => 4
            ]
        ];

        $this->loadTable($data);
    }
}