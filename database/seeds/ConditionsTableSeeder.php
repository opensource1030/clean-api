<?php

/**
 * ConditionsTableSeeder - Insert info into database.
 *
 * @author   Agustí Dosaiguas
 */
class ConditionsTableSeeder extends BaseTableSeeder
{
    protected $table = 'conditions';

    /**
     * Run the database seeds.
     */
    public function run()
    {
        $this->deleteTable();

        $data = [

            [
                'packageId' => 1,
                'name' => 'Supervisor?',
                'condition' => 'equal',
                'value' => 'No',
            ],
            [
                'packageId' => 1,
                'name' => 'Hierarchy',
                'condition' => 'contains',
                'value' => '1',
            ],
            [
                'packageId'  => 1,
                'name'      => "Level",
                'condition' => "greater than",
                'value'     => "2"
            ]
        ];

        $this->loadTable($data);
    }
}
