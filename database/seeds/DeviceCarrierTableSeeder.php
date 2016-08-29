<?php

/**
 * DeviceCarriersTableSeeder - Insert info into database.
 *  
 * @author   Agustí Dosaiguas
 */

class DeviceCarriersTableSeeder extends BaseTableSeeder
{
    protected $table = "device_carriers";

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
                'carrierId'      => 1
            ],
            [
                'deviceId'     => 1,
                'carrierId'      => 2
            ]
        ];

        $this->loadTable($data);
    }
}