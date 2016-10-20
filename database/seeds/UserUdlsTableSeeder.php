<?php

class UserUdlsTableSeeder extends BaseTableSeeder
{
    protected $table = 'employee_udls';

    public function run()
    {
        $this->deleteTable();

        $data = [
            [
                'userId' => 21,
                'udlValueId' => 5
            ],
            [
                'userId' => 21,
                'udlValueId' => 12
            ],
            [
                'userId' => 21,
                'udlValueId' => 27
            ],
            [
                'userId' => 21,
                'udlValueId' => 39
            ],
            [
                'userId' => 22,
                'udlValueId' => 5
            ],
            [
                'userId' => 22,
                'udlValueId' => 12
            ],
            [
                'userId' => 22,
                'udlValueId' => 27
            ],
            [
                'userId' => 22,
                'udlValueId' => 39
            ],
            [
                'userId' => 23,
                'udlValueId' => 5
            ],
            [
                'userId' => 23,
                'udlValueId' => 12
            ],
            [
                'userId' => 23,
                'udlValueId' => 27
            ],
            [
                'userId' => 23,
                'udlValueId' => 39
            ],
	    ];

	    $this->loadTable($data);
    }
}