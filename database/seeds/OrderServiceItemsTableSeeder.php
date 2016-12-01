<?php

/**
 * PackageAppsTableSeeder - Insert info into database.
 *
 * @author   Agustí Dosaiguas
 */
class OrderServiceItemsTableSeeder extends BaseTableSeeder
{
    protected $table = 'order_serviceitems';

    /**
     * Run the database seeds.
     */
    public function run()
    {
        $this->deleteTable();

        $data = [

            [
                'orderId' => 1,
                'serviceItemId' => 1,
            ],
            [
                'orderId' => 1,
                'serviceItemId' => 2,
            ],
            [
                'orderId' => 1,
                'serviceItemId' => 3,
            ],
            [
                'orderId' => 1,
                'serviceItemId' => 4,
            ],
            [
                'orderId' => 1,
                'serviceItemId' => 5,
            ],
            [
                'orderId' => 1,
                'serviceItemId' => 6,
            ],
            [
                'orderId' => 1,
                'serviceItemId' => 7,
            ],
            [
                'orderId' => 1,
                'serviceItemId' => 8,
            ],
            [
                'orderId' => 2,
                'serviceItemId' => 1,
            ],
            [
                'orderId' => 2,
                'serviceItemId' => 2,
            ],
            [
                'orderId' => 2,
                'serviceItemId' => 3,
            ],
            [
                'orderId' => 2,
                'serviceItemId' => 4,
            ],
            [
                'orderId' => 2,
                'serviceItemId' => 5,
            ],
            [
                'orderId' => 2,
                'serviceItemId' => 6,
            ],
            [
                'orderId' => 2,
                'serviceItemId' => 7,
            ],
            [
                'orderId' => 2,
                'serviceItemId' => 8,
            ],
            [
                'orderId' => 3,
                'serviceItemId' => 1,
            ],
            [
                'orderId' => 3,
                'serviceItemId' => 2,
            ],
            [
                'orderId' => 3,
                'serviceItemId' => 3,
            ],
            [
                'orderId' => 3,
                'serviceItemId' => 4,
            ],
            [
                'orderId' => 3,
                'serviceItemId' => 5,
            ],
            [
                'orderId' => 3,
                'serviceItemId' => 6,
            ],
            [
                'orderId' => 3,
                'serviceItemId' => 7,
            ],
            [
                'orderId' => 3,
                'serviceItemId' => 8,
            ]
        ];

        $this->loadTable($data);
    }
}
