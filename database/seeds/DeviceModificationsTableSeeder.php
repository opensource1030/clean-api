<?php

/**
 * DeviceCapacitiesTableSeeder - Insert info into database.
 *  
 * @author   Agustí Dosaiguas
 */

class DeviceModificationsTableSeeder extends BaseTableSeeder
{
    protected $table = "device_modifications";

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
                'modificationId'      => 1
            ],
            [
                'deviceId'     => 1,
                'modificationId'      => 2
            ]
        ];

        $this->loadTable($data);
    }
}