<?php

/**
 * OrdersTableSeeder - Insert info into database.
 *
 * @author   Agustí Dosaiguas
 */
class OrdersTableSeeder extends BaseTableSeeder
{
    protected $table = 'orders';

    /**
     * Run the database seeds.
     */
    public function run()
    {
        $this->deleteTable();

        $data = [

            [
                'status' => 'Denied',
                'userId' => 1,
                'packageId' => 1,
                'serviceId' => 1

            ],
            [
                'status' => 'Accepted',
                'userId' => 2,
                'packageId' => 2,
                'serviceId' => 2
            ],
            [
                'status' => 'Pending',
                'userId' => 3,
                'packageId' => 3,
                'serviceId' => 3
            ],
            [
                'status' => 'Accepted',
                'userId' => 4,
                'packageId' => 4,
                'serviceId' => 4
            ],
        ];

        $this->loadTable($data);
    }
}
