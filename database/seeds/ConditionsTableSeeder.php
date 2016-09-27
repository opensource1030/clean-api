<?php

/**
 * AppsTableSeeder - Insert info into database.
 *  
 * @author   Agustí Dosaiguas
 */

class ConditionsTableSeeder extends BaseTableSeeder
{
    protected $table = "conditions";

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
                'type'      => "type1",
                'name'      => "name1",
                'condition' => "condition1",
                'value'     => "value1"
            ],
            [
                'type'      => "type2",
                'name'      => "name2",
                'condition' => "condition2",
                'value'     => "value2"
            ],
            [
                'type'      => "type3",
                'name'      => "name3",
                'condition' => "condition3",
                'value'     => "value3"
            ],
            [
                'type'      => "type4",
                'name'      => "name4",
                'condition' => "condition4",
                'value'     => "value4"
            ],
        ];

        $this->loadTable($data);
    }
}