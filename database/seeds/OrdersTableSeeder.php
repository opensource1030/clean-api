<?php

/**
 * OrdersTableSeeder - Insert info into database.
 *  
 * @author   Agustí Dosaiguas
 */

class OrdersTableSeeder extends BaseTableSeeder
{
    protected $table = "orders";

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
                'status'     => "Denied",
                'userId'      => "WA-12345678",
                'packageId'      => 1
            ],
            [
                'status'     => "Accepted",
                'userId'      => "WA-13572468",
                'packageId'      => 2
            ],
            [
                'status'     => "Pending",
                'userId'      => "WA-24681357",
                'packageId'      => 3
            ],
            [
                'status'     => "Accepted",
                'userId'      => "WA-87654321",
                'packageId'      => 4
            ],
        ];

        $this->loadTable($data);
    }
}