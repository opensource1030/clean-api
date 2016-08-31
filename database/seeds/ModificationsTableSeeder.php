<?php

/**
 * CapacitiesTableSeeder - Insert info into database.
 *  
 * @author   Agustí Dosaiguas
 */

class ModificationsTableSeeder extends BaseTableSeeder
{
    protected $table = "modifications";

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
                'type'     => 'Capacity',
                'value'      => '16Gb'
            ],
            [
                'type'     => 'Style',
                'value'      => 'White'
            ],
            [
                'type'     => 'Capacity',
                'value'      => '8Gb'
            ],
            [
                'type'     => 'Capacity',
                'value'      => '128Gb'
            ],
            [
                'type'     => 'Style',
                'value'      => 'Gold'
            ],
            [
                'type'     => 'Style',
                'value'      => 'Space Grey'
            ]
        ];

        $this->loadTable($data);
    }
}