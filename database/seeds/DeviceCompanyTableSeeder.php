<?php

/**
 * DeviceProvidersTableSeeder - Insert info into database.
 *  
 * @author   Agustí Dosaiguas
 */

class DeviceProvidersTableSeeder extends BaseTableSeeder
{
    protected $table = "device_companies";

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
                'deviceId'     => 1,
                'companyId'      => 1
            ]
        ];

        $this->loadTable($data);
    }
}