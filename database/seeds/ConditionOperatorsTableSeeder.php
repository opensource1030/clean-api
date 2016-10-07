<?php

/**
 * ConditionOperatorsTableSeeder - Insert info into database.
 *  
 * @author   Agustí Dosaiguas
 */

class ConditionOperatorsTableSeeder extends BaseTableSeeder
{
    protected $table = "condition_operators";

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
                'originalName'      => "Like",
                'apiName'      => "like"
            ],
            [
                'originalName'      => "Greater Than",
                'apiName'      => "gt"
            ],
            [
                'originalName'      => "Less Than",
                'apiName'      => "lt"
            ],
            [
                'originalName'      => "Greater or Equal To",
                'apiName'      => "gte"
            ],
            [
                'originalName'      => "Less or Equal to",
                'apiName'      => "lte"
            ],
            [
                'originalName'      => "Not Equal To",
                'apiName'      => "ne"
            ],
            [
                'originalName'      => "Equal To",
                'apiName'      => "eq"
            ]
        ];

        $this->loadTable($data);
    }
}