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
                'deviceId' => 1,
                'serviceId' => 1,
                'carrierId' => 1,

            ],
            [
                'status' => 'Accepted',
                'userId' => 2,
                'packageId' => 2,
                'deviceId' => 2,
                'serviceId' => 2,
                'carrierId' => 2,
            ],
            [
                'status' => 'Pending',
                'userId' => 3,
                'packageId' => 3,
                'deviceId' => 3,
                'serviceId' => 3,
                'carrierId' => 3,
            ],
            [
                'status' => 'Accepted',
                'userId' => 4,
                'packageId' => 4,
                'deviceId' => 4,
                'serviceId' => 4,
                'carrierId' => 4,
            ],
        ];

        $this->loadTable($data);
    }
}
