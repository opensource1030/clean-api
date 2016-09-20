<?php

/**
 * PricesTableSeeder - Insert info into database.
 *  
 * @author   Agustí Dosaiguas
 */

class PricesTableSeeder extends BaseTableSeeder
{
    protected $table = "prices";

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
                'deviceId' => 1,
                'capacityId' => 1,
                'styleId' => 3,
                'carrierId' => 1,
                'companyId' => 1,
                'priceRetail' => 100,
                'price1' => 100,
                'price2' => 100,
                'priceOwn' => 100
            ],
            [
                'deviceId' => 1,
                'capacityId' => 2,
                'styleId' => 3,
                'carrierId' => 1,
                'companyId' => 1,
                'priceRetail' => 200,
                'price1' => 200,
                'price2' => 200,
                'priceOwn' => 200
            ],
            [
                'deviceId' => 1,
                'capacityId' => 1,
                'styleId' => 3,
                'carrierId' => 1,
                'companyId' => 2,
                'priceRetail' => 300,
                'price1' => 300,
                'price2' => 300,
                'priceOwn' => 300
            ],
            [
                'deviceId' => 1,
                'capacityId' => 2,
                'styleId' => 3,
                'carrierId' => 1,
                'companyId' => 2,
                'priceRetail' => 400,
                'price1' => 400,
                'price2' => 400,
                'priceOwn' => 400
            ],
            [
                'deviceId' => 1,
                'capacityId' => 1,
                'styleId' => 3,
                'carrierId' => 2,
                'companyId' => 1,
                'priceRetail' => 500,
                'price1' => 500,
                'price2' => 500,
                'priceOwn' => 500
            ],
            [
                'deviceId' => 1,
                'capacityId' => 2,
                'styleId' => 3,
                'carrierId' => 2,
                'companyId' => 1,
                'priceRetail' => 600,
                'price1' => 600,
                'price2' => 600,
                'priceOwn' => 600
            ],
            [
                'deviceId' => 1,
                'capacityId' => 1,
                'styleId' => 3,
                'carrierId' => 2,
                'companyId' => 2,
                'priceRetail' => 700,
                'price1' => 700,
                'price2' => 700,
                'priceOwn' => 700
            ],
            [
                'deviceId' => 1,
                'capacityId' => 2,
                'styleId' => 3,
                'carrierId' => 2,
                'companyId' => 2,
                'priceRetail' => 800,
                'price1' => 800,
                'price2' => 800,
                'priceOwn' => 800
            ]
        ];

        $this->loadTable($data);
    }
}