<?php

/**
 * DeviceStylesTableSeeder - Insert info into database.
 *  
 * @author   Agustí Dosaiguas
 */

class DeviceStylesTableSeeder extends BaseTableSeeder
{
    protected $table = "device_styles";

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
                'styleId'      => 1
            ],
            [
                'deviceId'     => 1,
                'styleId'      => 2
            ]
        ];

        $this->loadTable($data);
    }
}