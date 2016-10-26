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
                'typeCond' => 'Employee Profile',
                'name' => 'Name',
                'condition' => '',
                'value' => '',
            ],
            [
                'typeCond' => 'Employee Profile',
                'name' => 'Email',
                'condition' => '',
                'value' => '',
            ],
            [
                'typeCond' => 'Employee Profile',
                'name' => 'Position',
                'condition' => 'like',
                'value' => 'Engineer',
            ],
            [
                'typeCond' => 'Employee Profile',
                'name' => 'Level',
                'condition' => 'gt',
                'value' => '3',
            ],
            [
                'typeCond' => 'Employee Profile',
                'name' => 'Division',
                'condition' => 'like',
                'value' => 'Sales',
            ],
            [
                'typeCond' => 'Employee Profile',
                'name' => 'Cost Center',
                'condition' => '',
                'value' => '',
            ],
            [
                'typeCond' => 'Employee Profile',
                'name' => 'Budget',
                'condition' => 'lt',
                'value' => '600',
            ],
            [
                'typeCond' => 'Location',
                'name' => 'Country',
                'condition' => 'contains',
                'value' => 'USA',
            ],
            [
                'typeCond' => 'Employee Profile',
                'name' => 'Country',
                'condition' => 'contains',
                'value' => 'Canada',
            ],
            [
                'typeCond' => 'Employee Profile',
                'name' => 'City',
                'condition' => '',
                'value' => '',
            ],
            [
                'typeCond' => 'Employee Profile',
                'name' => 'Address',
                'condition' => '',
                'value' => '',
            ],
        ];

        $this->loadTable($data);
    }
}
