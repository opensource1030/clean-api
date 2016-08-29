<?php

/**
 * DeviceCapacitiesTableSeeder - Insert info into database.
 *  
 * @author   Agustí Dosaiguas
 */

class DeviceCapacitiesTableSeeder extends BaseTableSeeder
{
    protected $table = "device_capacities";

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
                'capacityId'      => 1
            ],
            [
                'deviceId'     => 1,
                'capacityId'      => 2
            ]
        ];

        $this->loadTable($data);
    }
}