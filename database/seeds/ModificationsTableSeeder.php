<?php

/**
 * capacitiesTableSeeder - Insert info into database.
 *
 * @author   Agustí Dosaiguas
 */
class ModificationsTableSeeder extends BaseTableSeeder
{
    protected $table = 'modifications';

    /**
     * Run the database seeds.
     */
    public function run()
    {
        $this->deleteTable();

        $data = [

            [
                'modType' => 'capacity',
                'value' => '16Gb',
            ],
            [
                'modType' => 'style',
                'value' => 'White',
            ],
            [
                'modType' => 'capacity',
                'value' => '8Gb',
            ],
            [
                'modType' => 'capacity',
                'value' => '128Gb',
            ],
            [
                'modType' => 'style',
                'value' => 'Gold',
            ],
            [
                'modType' => 'style',
                'value' => 'Space Grey',
            ],
        ];

        $this->loadTable($data);
    }
}
