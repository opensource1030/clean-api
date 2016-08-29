<?php

/**
 * StylesTableSeeder - Insert info into database.
 *  
 * @author   Agustí Dosaiguas
 */

class StylesTableSeeder extends BaseTableSeeder
{
    protected $table = "styles";

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
                'name'      => 'White'
            ],
            [
                'name'      => 'Silver'
            ],
            [
                'name'      => 'Gold'
            ],
            [
                'name'      => 'Grey'
            ],
            [
                'name'      => 'Space Grey'
            ],
            [
                'name'      => 'Pink Gold'
            ]
        ];

        $this->loadTable($data);
    }
}