<?php

/**
 * PricesTableSeeder - Insert info into database.
 *  
 * @author   Agustí Dosaiguas
 */

class PricesTableSeeder extends BaseTableSeeder
{
    protected $table = "device_prices";

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
                'priceRetail'              => 399,
                'price1'              => 399,
                'price2'              => 399,
                'priceOwn'              => 399,
                'deviceId'              => 1,
                'modificationId'              => 1,
                'carrierId'              => 1,
                'providerId'              => 4
            ],
            [
                'priceRetail'              => 399,
                'price1'              => 399,
                'price2'              => 399,
                'priceOwn'              => 399,
                'deviceId'              => 1,
                'modificationId'              => 1,
                'carrierId'              => 2,
                'providerId'              => 4
            ],
            [
                'priceRetail'              => 399,
                'price1'              => 399,
                'price2'              => 399,
                'priceOwn'              => 399,
                'deviceId'              => 1,
                'modificationId'              => 1,
                'carrierId'              => 1,
                'providerId'              => 4
            ],
            [
                'priceRetail'              => 399,
                'price1'              => 399,
                'price2'              => 399,
                'priceOwn'              => 399,
                'deviceId'              => 1,
                'modificationId'              => 1,
                'carrierId'              => 2,
                'providerId'              => 4
            ],
            [
                'priceRetail'              => 399,
                'price1'              => 399,
                'price2'              => 399,
                'priceOwn'              => 399,
                'deviceId'              => 1,
                'modificationId'              => 2,
                'carrierId'              => 1,
                'providerId'              => 4
            ],
            [
                'priceRetail'              => 399,
                'price1'              => 399,
                'price2'              => 399,
                'priceOwn'              => 399,
                'deviceId'              => 1,
                'modificationId'              => 2,
                'carrierId'              => 2,
                'providerId'              => 4
            ],
                        [
                'priceRetail'              => 399,
                'price1'              => 399,
                'price2'              => 399,
                'priceOwn'              => 399,
                'deviceId'              => 1,
                'modificationId'              => 2,
                'carrierId'              => 1,
                'providerId'              => 4
            ],
            [
                'priceRetail'              => 399,
                'price1'              => 399,
                'price2'              => 399,
                'priceOwn'              => 399,
                'deviceId'              => 1,
                'modificationId'              => 2,
                'carrierId'              => 2,
                'providerId'              => 4
            ]
        ];

        $this->loadTable($data);
    }
}