<?php

/**
 * PricesTableSeeder - Insert info into database.
 *
 * @author   Agustí Dosaiguas
 */
class PricesTableSeeder extends BaseTableSeeder
{
    protected $table = 'prices';

    /**
     * Run the database seeds.
     */
    public function run()
    {
        $this->deleteTable();

        $dataDevice1 = [

            [
                'deviceId' => 1,
                'capacityId' => 1,
                'styleId' => 2,
                'carrierId' => 1,
                'companyId' => 1,
                'priceRetail' => 100,
                'price1' => 100,
                'price2' => 100,
                'priceOwn' => 100,
            ],
            [
                'deviceId' => 1,
                'capacityId' => 1,
                'styleId' => 2,
                'carrierId' => 1,
                'companyId' => 2,
                'priceRetail' => 200,
                'price1' => 200,
                'price2' => 200,
                'priceOwn' => 200,
            ],
            [
                'deviceId' => 1,
                'capacityId' => 1,
                'styleId' => 2,
                'carrierId' => 1,
                'companyId' => 3,
                'priceRetail' => 300,
                'price1' => 300,
                'price2' => 300,
                'priceOwn' => 300,
            ],
            [
                'deviceId' => 1,
                'capacityId' => 3,
                'styleId' => 2,
                'carrierId' => 1,
                'companyId' => 1,
                'priceRetail' => 100,
                'price1' => 100,
                'price2' => 100,
                'priceOwn' => 100,
            ],
            [
                'deviceId' => 1,
                'capacityId' => 3,
                'styleId' => 2,
                'carrierId' => 1,
                'companyId' => 2,
                'priceRetail' => 200,
                'price1' => 200,
                'price2' => 200,
                'priceOwn' => 200,
            ],
            [
                'deviceId' => 1,
                'capacityId' => 3,
                'styleId' => 2,
                'carrierId' => 1,
                'companyId' => 3,
                'priceRetail' => 300,
                'price1' => 300,
                'price2' => 300,
                'priceOwn' => 300,
            ],
            [
                'deviceId' => 1,
                'capacityId' => 4,
                'styleId' => 2,
                'carrierId' => 1,
                'companyId' => 1,
                'priceRetail' => 100,
                'price1' => 100,
                'price2' => 100,
                'priceOwn' => 100,
            ],
            [
                'deviceId' => 1,
                'capacityId' => 4,
                'styleId' => 2,
                'carrierId' => 1,
                'companyId' => 2,
                'priceRetail' => 200,
                'price1' => 200,
                'price2' => 200,
                'priceOwn' => 200,
            ],
            [
                'deviceId' => 1,
                'capacityId' => 4,
                'styleId' => 2,
                'carrierId' => 1,
                'companyId' => 3,
                'priceRetail' => 300,
                'price1' => 300,
                'price2' => 300,
                'priceOwn' => 300,
            ],
            [
                'deviceId' => 1,
                'capacityId' => 1,
                'styleId' => 2,
                'carrierId' => 2,
                'companyId' => 1,
                'priceRetail' => 100,
                'price1' => 100,
                'price2' => 100,
                'priceOwn' => 100,
            ],
            [
                'deviceId' => 1,
                'capacityId' => 1,
                'styleId' => 2,
                'carrierId' => 2,
                'companyId' => 2,
                'priceRetail' => 200,
                'price1' => 200,
                'price2' => 200,
                'priceOwn' => 200,
            ],
            [
                'deviceId' => 1,
                'capacityId' => 1,
                'styleId' => 2,
                'carrierId' => 2,
                'companyId' => 3,
                'priceRetail' => 300,
                'price1' => 300,
                'price2' => 300,
                'priceOwn' => 300,
            ],
            [
                'deviceId' => 1,
                'capacityId' => 3,
                'styleId' => 2,
                'carrierId' => 2,
                'companyId' => 1,
                'priceRetail' => 100,
                'price1' => 100,
                'price2' => 100,
                'priceOwn' => 100,
            ],
            [
                'deviceId' => 1,
                'capacityId' => 3,
                'styleId' => 2,
                'carrierId' => 2,
                'companyId' => 2,
                'priceRetail' => 200,
                'price1' => 200,
                'price2' => 200,
                'priceOwn' => 200,
            ],
            [
                'deviceId' => 1,
                'capacityId' => 3,
                'styleId' => 2,
                'carrierId' => 2,
                'companyId' => 3,
                'priceRetail' => 300,
                'price1' => 300,
                'price2' => 300,
                'priceOwn' => 300,
            ],
            [
                'deviceId' => 1,
                'capacityId' => 4,
                'styleId' => 2,
                'carrierId' => 2,
                'companyId' => 1,
                'priceRetail' => 100,
                'price1' => 100,
                'price2' => 100,
                'priceOwn' => 100,
            ],
            [
                'deviceId' => 1,
                'capacityId' => 4,
                'styleId' => 2,
                'carrierId' => 2,
                'companyId' => 2,
                'priceRetail' => 200,
                'price1' => 200,
                'price2' => 200,
                'priceOwn' => 200,
            ],
            [
                'deviceId' => 1,
                'capacityId' => 4,
                'styleId' => 2,
                'carrierId' => 2,
                'companyId' => 3,
                'priceRetail' => 300,
                'price1' => 300,
                'price2' => 300,
                'priceOwn' => 300,
            ],
            [
                'deviceId' => 1,
                'capacityId' => 1,
                'styleId' => 2,
                'carrierId' => 3,
                'companyId' => 1,
                'priceRetail' => 100,
                'price1' => 100,
                'price2' => 100,
                'priceOwn' => 100,
            ],
            [
                'deviceId' => 1,
                'capacityId' => 1,
                'styleId' => 2,
                'carrierId' => 3,
                'companyId' => 2,
                'priceRetail' => 200,
                'price1' => 200,
                'price2' => 200,
                'priceOwn' => 200,
            ],
            [
                'deviceId' => 1,
                'capacityId' => 1,
                'styleId' => 2,
                'carrierId' => 3,
                'companyId' => 3,
                'priceRetail' => 300,
                'price1' => 300,
                'price2' => 300,
                'priceOwn' => 300,
            ],
            [
                'deviceId' => 1,
                'capacityId' => 3,
                'styleId' => 2,
                'carrierId' => 3,
                'companyId' => 1,
                'priceRetail' => 100,
                'price1' => 100,
                'price2' => 100,
                'priceOwn' => 100,
            ],
            [
                'deviceId' => 1,
                'capacityId' => 3,
                'styleId' => 2,
                'carrierId' => 3,
                'companyId' => 2,
                'priceRetail' => 200,
                'price1' => 200,
                'price2' => 200,
                'priceOwn' => 200,
            ],
            [
                'deviceId' => 1,
                'capacityId' => 3,
                'styleId' => 2,
                'carrierId' => 3,
                'companyId' => 3,
                'priceRetail' => 300,
                'price1' => 300,
                'price2' => 300,
                'priceOwn' => 300,
            ],
            [
                'deviceId' => 1,
                'capacityId' => 4,
                'styleId' => 2,
                'carrierId' => 3,
                'companyId' => 1,
                'priceRetail' => 100,
                'price1' => 100,
                'price2' => 100,
                'priceOwn' => 100,
            ],
            [
                'deviceId' => 1,
                'capacityId' => 4,
                'styleId' => 2,
                'carrierId' => 3,
                'companyId' => 2,
                'priceRetail' => 200,
                'price1' => 200,
                'price2' => 200,
                'priceOwn' => 200,
            ],
            [
                'deviceId' => 1,
                'capacityId' => 4,
                'styleId' => 2,
                'carrierId' => 3,
                'companyId' => 3,
                'priceRetail' => 300,
                'price1' => 300,
                'price2' => 300,
                'priceOwn' => 300,
            ],
        ];

        $dataDevice2 = [
            [
                'deviceId' => 2,
                'capacityId' => 1,
                'styleId' => 2,
                'carrierId' => 1,
                'companyId' => 1,
                'priceRetail' => 100,
                'price1' => 100,
                'price2' => 100,
                'priceOwn' => 100,
            ],
            [
                'deviceId' => 2,
                'capacityId' => 1,
                'styleId' => 2,
                'carrierId' => 1,
                'companyId' => 4,
                'priceRetail' => 200,
                'price1' => 200,
                'price2' => 200,
                'priceOwn' => 200,
            ],
            [
                'deviceId' => 2,
                'capacityId' => 1,
                'styleId' => 2,
                'carrierId' => 1,
                'companyId' => 5,
                'priceRetail' => 300,
                'price1' => 300,
                'price2' => 300,
                'priceOwn' => 300,
            ],
            [
                'deviceId' => 2,
                'capacityId' => 1,
                'styleId' => 2,
                'carrierId' => 5,
                'companyId' => 1,
                'priceRetail' => 100,
                'price1' => 100,
                'price2' => 100,
                'priceOwn' => 100,
            ],
            [
                'deviceId' => 2,
                'capacityId' => 1,
                'styleId' => 2,
                'carrierId' => 5,
                'companyId' => 4,
                'priceRetail' => 200,
                'price1' => 200,
                'price2' => 200,
                'priceOwn' => 200,
            ],
            [
                'deviceId' => 2,
                'capacityId' => 1,
                'styleId' => 2,
                'carrierId' => 5,
                'companyId' => 5,
                'priceRetail' => 300,
                'price1' => 300,
                'price2' => 300,
                'priceOwn' => 300,
            ],
            [
                'deviceId' => 2,
                'capacityId' => 1,
                'styleId' => 2,
                'carrierId' => 6,
                'companyId' => 1,
                'priceRetail' => 100,
                'price1' => 100,
                'price2' => 100,
                'priceOwn' => 100,
            ],
            [
                'deviceId' => 2,
                'capacityId' => 1,
                'styleId' => 2,
                'carrierId' => 6,
                'companyId' => 4,
                'priceRetail' => 200,
                'price1' => 200,
                'price2' => 200,
                'priceOwn' => 200,
            ],
            [
                'deviceId' => 2,
                'capacityId' => 1,
                'styleId' => 2,
                'carrierId' => 6,
                'companyId' => 5,
                'priceRetail' => 300,
                'price1' => 300,
                'price2' => 300,
                'priceOwn' => 300,
            ],
            [
                'deviceId' => 2,
                'capacityId' => 1,
                'styleId' => 5,
                'carrierId' => 1,
                'companyId' => 1,
                'priceRetail' => 100,
                'price1' => 100,
                'price2' => 100,
                'priceOwn' => 100,
            ],
            [
                'deviceId' => 2,
                'capacityId' => 1,
                'styleId' => 5,
                'carrierId' => 1,
                'companyId' => 4,
                'priceRetail' => 200,
                'price1' => 200,
                'price2' => 200,
                'priceOwn' => 200,
            ],
            [
                'deviceId' => 2,
                'capacityId' => 1,
                'styleId' => 5,
                'carrierId' => 1,
                'companyId' => 5,
                'priceRetail' => 300,
                'price1' => 300,
                'price2' => 300,
                'priceOwn' => 300,
            ],
            [
                'deviceId' => 2,
                'capacityId' => 1,
                'styleId' => 5,
                'carrierId' => 5,
                'companyId' => 1,
                'priceRetail' => 100,
                'price1' => 100,
                'price2' => 100,
                'priceOwn' => 100,
            ],
            [
                'deviceId' => 2,
                'capacityId' => 1,
                'styleId' => 5,
                'carrierId' => 5,
                'companyId' => 4,
                'priceRetail' => 200,
                'price1' => 200,
                'price2' => 200,
                'priceOwn' => 200,
            ],
            [
                'deviceId' => 2,
                'capacityId' => 1,
                'styleId' => 5,
                'carrierId' => 5,
                'companyId' => 5,
                'priceRetail' => 300,
                'price1' => 300,
                'price2' => 300,
                'priceOwn' => 300,
            ],
            [
                'deviceId' => 2,
                'capacityId' => 1,
                'styleId' => 5,
                'carrierId' => 6,
                'companyId' => 1,
                'priceRetail' => 100,
                'price1' => 100,
                'price2' => 100,
                'priceOwn' => 100,
            ],
            [
                'deviceId' => 2,
                'capacityId' => 1,
                'styleId' => 5,
                'carrierId' => 6,
                'companyId' => 4,
                'priceRetail' => 200,
                'price1' => 200,
                'price2' => 200,
                'priceOwn' => 200,
            ],
            [
                'deviceId' => 2,
                'capacityId' => 1,
                'styleId' => 5,
                'carrierId' => 6,
                'companyId' => 5,
                'priceRetail' => 300,
                'price1' => 300,
                'price2' => 300,
                'priceOwn' => 300,
            ],
            [
                'deviceId' => 2,
                'capacityId' => 3,
                'styleId' => 2,
                'carrierId' => 1,
                'companyId' => 1,
                'priceRetail' => 100,
                'price1' => 100,
                'price2' => 100,
                'priceOwn' => 100,
            ],
            [
                'deviceId' => 2,
                'capacityId' => 3,
                'styleId' => 2,
                'carrierId' => 1,
                'companyId' => 4,
                'priceRetail' => 200,
                'price1' => 200,
                'price2' => 200,
                'priceOwn' => 200,
            ],
            [
                'deviceId' => 2,
                'capacityId' => 3,
                'styleId' => 2,
                'carrierId' => 1,
                'companyId' => 5,
                'priceRetail' => 300,
                'price1' => 300,
                'price2' => 300,
                'priceOwn' => 300,
            ],
            [
                'deviceId' => 2,
                'capacityId' => 3,
                'styleId' => 2,
                'carrierId' => 5,
                'companyId' => 1,
                'priceRetail' => 100,
                'price1' => 100,
                'price2' => 100,
                'priceOwn' => 100,
            ],
            [
                'deviceId' => 2,
                'capacityId' => 3,
                'styleId' => 2,
                'carrierId' => 5,
                'companyId' => 4,
                'priceRetail' => 200,
                'price1' => 200,
                'price2' => 200,
                'priceOwn' => 200,
            ],
            [
                'deviceId' => 2,
                'capacityId' => 3,
                'styleId' => 2,
                'carrierId' => 5,
                'companyId' => 5,
                'priceRetail' => 300,
                'price1' => 300,
                'price2' => 300,
                'priceOwn' => 300,
            ],
            [
                'deviceId' => 2,
                'capacityId' => 3,
                'styleId' => 2,
                'carrierId' => 6,
                'companyId' => 1,
                'priceRetail' => 100,
                'price1' => 100,
                'price2' => 100,
                'priceOwn' => 100,
            ],
            [
                'deviceId' => 2,
                'capacityId' => 3,
                'styleId' => 2,
                'carrierId' => 6,
                'companyId' => 4,
                'priceRetail' => 200,
                'price1' => 200,
                'price2' => 200,
                'priceOwn' => 200,
            ],
            [
                'deviceId' => 2,
                'capacityId' => 3,
                'styleId' => 2,
                'carrierId' => 6,
                'companyId' => 5,
                'priceRetail' => 300,
                'price1' => 300,
                'price2' => 300,
                'priceOwn' => 300,
            ],
            [
                'deviceId' => 2,
                'capacityId' => 3,
                'styleId' => 5,
                'carrierId' => 1,
                'companyId' => 1,
                'priceRetail' => 100,
                'price1' => 100,
                'price2' => 100,
                'priceOwn' => 100,
            ],
            [
                'deviceId' => 2,
                'capacityId' => 3,
                'styleId' => 5,
                'carrierId' => 1,
                'companyId' => 4,
                'priceRetail' => 200,
                'price1' => 200,
                'price2' => 200,
                'priceOwn' => 200,
            ],
            [
                'deviceId' => 2,
                'capacityId' => 3,
                'styleId' => 5,
                'carrierId' => 1,
                'companyId' => 5,
                'priceRetail' => 300,
                'price1' => 300,
                'price2' => 300,
                'priceOwn' => 300,
            ],
            [
                'deviceId' => 2,
                'capacityId' => 3,
                'styleId' => 5,
                'carrierId' => 5,
                'companyId' => 1,
                'priceRetail' => 100,
                'price1' => 100,
                'price2' => 100,
                'priceOwn' => 100,
            ],
            [
                'deviceId' => 2,
                'capacityId' => 3,
                'styleId' => 5,
                'carrierId' => 5,
                'companyId' => 4,
                'priceRetail' => 200,
                'price1' => 200,
                'price2' => 200,
                'priceOwn' => 200,
            ],
            [
                'deviceId' => 2,
                'capacityId' => 3,
                'styleId' => 5,
                'carrierId' => 5,
                'companyId' => 5,
                'priceRetail' => 300,
                'price1' => 300,
                'price2' => 300,
                'priceOwn' => 300,
            ],
            [
                'deviceId' => 2,
                'capacityId' => 3,
                'styleId' => 5,
                'carrierId' => 6,
                'companyId' => 1,
                'priceRetail' => 100,
                'price1' => 100,
                'price2' => 100,
                'priceOwn' => 100,
            ],
            [
                'deviceId' => 2,
                'capacityId' => 3,
                'styleId' => 5,
                'carrierId' => 6,
                'companyId' => 4,
                'priceRetail' => 200,
                'price1' => 200,
                'price2' => 200,
                'priceOwn' => 200,
            ],
            [
                'deviceId' => 2,
                'capacityId' => 3,
                'styleId' => 5,
                'carrierId' => 6,
                'companyId' => 5,
                'priceRetail' => 300,
                'price1' => 300,
                'price2' => 300,
                'priceOwn' => 300,
            ],
        ];

        $dataDevice3 = [
            [
                'deviceId' => 3,
                'capacityId' => 1,
                'styleId' => 2,
                'carrierId' => 2,
                'companyId' => 2,
                'priceRetail' => 100,
                'price1' => 100,
                'price2' => 100,
                'priceOwn' => 100,
            ],
            [
                'deviceId' => 3,
                'capacityId' => 1,
                'styleId' => 2,
                'carrierId' => 2,
                'companyId' => 5,
                'priceRetail' => 200,
                'price1' => 200,
                'price2' => 200,
                'priceOwn' => 200,
            ],
            [
                'deviceId' => 3,
                'capacityId' => 1,
                'styleId' => 2,
                'carrierId' => 2,
                'companyId' => 6,
                'priceRetail' => 300,
                'price1' => 300,
                'price2' => 300,
                'priceOwn' => 300,
            ],
            [
                'deviceId' => 3,
                'capacityId' => 1,
                'styleId' => 2,
                'carrierId' => 11,
                'companyId' => 2,
                'priceRetail' => 100,
                'price1' => 100,
                'price2' => 100,
                'priceOwn' => 100,
            ],
            [
                'deviceId' => 3,
                'capacityId' => 1,
                'styleId' => 2,
                'carrierId' => 11,
                'companyId' => 5,
                'priceRetail' => 200,
                'price1' => 200,
                'price2' => 200,
                'priceOwn' => 200,
            ],
            [
                'deviceId' => 3,
                'capacityId' => 1,
                'styleId' => 2,
                'carrierId' => 11,
                'companyId' => 6,
                'priceRetail' => 300,
                'price1' => 300,
                'price2' => 300,
                'priceOwn' => 300,
            ],
            [
                'deviceId' => 3,
                'capacityId' => 1,
                'styleId' => 2,
                'carrierId' => 13,
                'companyId' => 2,
                'priceRetail' => 100,
                'price1' => 100,
                'price2' => 100,
                'priceOwn' => 100,
            ],
            [
                'deviceId' => 3,
                'capacityId' => 1,
                'styleId' => 2,
                'carrierId' => 13,
                'companyId' => 5,
                'priceRetail' => 200,
                'price1' => 200,
                'price2' => 200,
                'priceOwn' => 200,
            ],
            [
                'deviceId' => 3,
                'capacityId' => 1,
                'styleId' => 2,
                'carrierId' => 13,
                'companyId' => 6,
                'priceRetail' => 300,
                'price1' => 300,
                'price2' => 300,
                'priceOwn' => 300,
            ],
            [
                'deviceId' => 3,
                'capacityId' => 1,
                'styleId' => 5,
                'carrierId' => 2,
                'companyId' => 2,
                'priceRetail' => 100,
                'price1' => 100,
                'price2' => 100,
                'priceOwn' => 100,
            ],
            [
                'deviceId' => 3,
                'capacityId' => 1,
                'styleId' => 5,
                'carrierId' => 2,
                'companyId' => 5,
                'priceRetail' => 200,
                'price1' => 200,
                'price2' => 200,
                'priceOwn' => 200,
            ],
            [
                'deviceId' => 3,
                'capacityId' => 1,
                'styleId' => 5,
                'carrierId' => 2,
                'companyId' => 6,
                'priceRetail' => 300,
                'price1' => 300,
                'price2' => 300,
                'priceOwn' => 300,
            ],
            [
                'deviceId' => 3,
                'capacityId' => 1,
                'styleId' => 5,
                'carrierId' => 11,
                'companyId' => 2,
                'priceRetail' => 100,
                'price1' => 100,
                'price2' => 100,
                'priceOwn' => 100,
            ],
            [
                'deviceId' => 3,
                'capacityId' => 1,
                'styleId' => 5,
                'carrierId' => 11,
                'companyId' => 5,
                'priceRetail' => 200,
                'price1' => 200,
                'price2' => 200,
                'priceOwn' => 200,
            ],
            [
                'deviceId' => 3,
                'capacityId' => 1,
                'styleId' => 5,
                'carrierId' => 11,
                'companyId' => 6,
                'priceRetail' => 300,
                'price1' => 300,
                'price2' => 300,
                'priceOwn' => 300,
            ],
            [
                'deviceId' => 3,
                'capacityId' => 1,
                'styleId' => 5,
                'carrierId' => 13,
                'companyId' => 2,
                'priceRetail' => 100,
                'price1' => 100,
                'price2' => 100,
                'priceOwn' => 100,
            ],
            [
                'deviceId' => 3,
                'capacityId' => 1,
                'styleId' => 5,
                'carrierId' => 13,
                'companyId' => 5,
                'priceRetail' => 200,
                'price1' => 200,
                'price2' => 200,
                'priceOwn' => 200,
            ],
            [
                'deviceId' => 3,
                'capacityId' => 1,
                'styleId' => 5,
                'carrierId' => 13,
                'companyId' => 6,
                'priceRetail' => 300,
                'price1' => 300,
                'price2' => 300,
                'priceOwn' => 300,
            ],
            [
                'deviceId' => 3,
                'capacityId' => 1,
                'styleId' => 6,
                'carrierId' => 2,
                'companyId' => 2,
                'priceRetail' => 100,
                'price1' => 100,
                'price2' => 100,
                'priceOwn' => 100,
            ],
            [
                'deviceId' => 3,
                'capacityId' => 1,
                'styleId' => 6,
                'carrierId' => 2,
                'companyId' => 5,
                'priceRetail' => 200,
                'price1' => 200,
                'price2' => 200,
                'priceOwn' => 200,
            ],
            [
                'deviceId' => 3,
                'capacityId' => 1,
                'styleId' => 6,
                'carrierId' => 2,
                'companyId' => 6,
                'priceRetail' => 300,
                'price1' => 300,
                'price2' => 300,
                'priceOwn' => 300,
            ],
            [
                'deviceId' => 3,
                'capacityId' => 1,
                'styleId' => 6,
                'carrierId' => 11,
                'companyId' => 2,
                'priceRetail' => 100,
                'price1' => 100,
                'price2' => 100,
                'priceOwn' => 100,
            ],
            [
                'deviceId' => 3,
                'capacityId' => 1,
                'styleId' => 6,
                'carrierId' => 11,
                'companyId' => 5,
                'priceRetail' => 200,
                'price1' => 200,
                'price2' => 200,
                'priceOwn' => 200,
            ],
            [
                'deviceId' => 3,
                'capacityId' => 1,
                'styleId' => 6,
                'carrierId' => 11,
                'companyId' => 6,
                'priceRetail' => 300,
                'price1' => 300,
                'price2' => 300,
                'priceOwn' => 300,
            ],
            [
                'deviceId' => 3,
                'capacityId' => 1,
                'styleId' => 6,
                'carrierId' => 13,
                'companyId' => 2,
                'priceRetail' => 100,
                'price1' => 100,
                'price2' => 100,
                'priceOwn' => 100,
            ],
            [
                'deviceId' => 3,
                'capacityId' => 1,
                'styleId' => 6,
                'carrierId' => 13,
                'companyId' => 5,
                'priceRetail' => 200,
                'price1' => 200,
                'price2' => 200,
                'priceOwn' => 200,
            ],
            [
                'deviceId' => 3,
                'capacityId' => 1,
                'styleId' => 6,
                'carrierId' => 13,
                'companyId' => 6,
                'priceRetail' => 300,
                'price1' => 300,
                'price2' => 300,
                'priceOwn' => 300,
            ],
        ];

        $this->loadTable($dataDevice1);
        $this->loadTable($dataDevice2);
        $this->loadTable($dataDevice3);
    }
}