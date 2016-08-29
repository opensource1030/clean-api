<?php

/**
 * CapacitiesTableSeeder - Insert info into database.
 *  
 * @author   Agustí Dosaiguas
 */

class CapacitiesTableSeeder extends BaseTableSeeder
{
    protected $table = "capacities";

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
                'value'     => '16',
                'type'      => 'Gb'
            ],
            [
                'value'     => '32',
                'type'      => 'Gb'
            ],
            [
                'value'     => '64',
                'type'      => 'Gb'
            ],
            [
                'value'     => '128',
                'type'      => 'Gb'
            ],
            [
                'value'     => '8',
                'type'      => 'Gb'
            ],
            [
                'value'     => '512',
                'type'      => 'Mb'
            ]
        ];

        $this->loadTable($data);
    }
}