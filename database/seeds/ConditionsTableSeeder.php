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
                'typeCond'  => "Employee Profile",
                'name'      => "Position",
                'condition' => "like",
                'value'     => "Position5"
            ],
            [
                'typeCond'  => "Employee Profile",
                'name'      => "Level",
                'condition' => "like",
                'value'     => "Level2"
            ],
            [
                'typeCond'  => "Employee Profile",
                'name'      => "Division",
                'condition' => "like",
                'value'     => "Division7"
            ],
            [
                'typeCond'  => "Employee Profile",
                'name'      => "Cost Center",
                'condition' => "like",
                'value'     => "Cost Center9"
            ],
            [
                'typeCond'  => "Employee Profile",
                'name'      => "Budget",
                'condition' => "",
                'value'     => ""
            ],
            [
                'typeCond'  => "Location",
                'name'      => "Country",
                'condition' => "",
                'value'     => ""
            ],
            [
                'typeCond'  => "Employee Profile",
                'name'      => "Country",
                'condition' => "",
                'value'     => ""
            ],
            [
                'typeCond'  => "Employee Profile",
                'name'      => "City",
                'condition' => "",
                'value'     => ""
            ],
            [
                'typeCond'  => "Employee Profile",
                'name'      => "Address",
                'condition' => "",
                'value'     => ""
            ],
            [
                'typeCond'  => "Employee Profile",
                'name'      => "Cost Center",
                'condition' => "like",
                'value'     => "Cost Center1"
            ],
            [
                'typeCond'  => "Employee Profile",
                'name'      => "Cost Center",
                'condition' => "like",
                'value'     => "Cost Center5"
            ],
            [
                'typeCond'  => "Employee Profile",
                'name'      => "Cost Center",
                'condition' => "like",
                'value'     => "Cost Center3"
            ]
        ];

        $this->loadTable($data);
    }
}
