<?php

/**
 * capacitiesTableSeeder - Insert info into database.
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
                'type'     => 'capacity',
                'value'      => '16Gb'
            ],
            [
                'type'     => 'style',
                'value'      => 'White'
            ],
            [
                'type'     => 'capacity',
                'value'      => '8Gb'
            ],
            [
                'type'     => 'capacity',
                'value'      => '128Gb'
            ],
            [
                'type'     => 'style',
                'value'      => 'Gold'
            ],
            [
                'type'     => 'style',
                'value'      => 'Space Grey'
            ]
        ];

        $this->loadTable($data);
    }
}