<?php

/**
 * DeviceVariantsTableSeeder - Insert info into database.
 *
 * @author   Agustí Dosaiguas
 */
class DeviceVariationsTableSeeder extends BaseTableSeeder
{
    protected $table = 'device_variations';

    /**
     * Run the database seeds.
     */
    public function run()
    {
        $this->deleteTable();

        $dataDevice1 = [

            [
                'deviceId' => 1,
                'carrierId' => 1,
                'companyId' => 1,
                'priceRetail' => 100,
                'price1' => 100,
                'price2' => 100,
                'priceOwn' => 100,
            ],          
        ];

        $dataDevice2 = [
            [
                'deviceId' => 2,
                'carrierId' => 1,
                'companyId' => 1,
                'priceRetail' => 100,
                'price1' => 100,
                'price2' => 100,
                'priceOwn' => 100,
            ],      
        ];

        $dataDevice3 = [
            [
                'deviceId' => 3,
                'carrierId' => 2,
                'companyId' => 2,
                'priceRetail' => 100,
                'price1' => 100,
                'price2' => 100,
                'priceOwn' => 100,
            ],           
        ];

        $this->loadTable($dataDevice1);
        $this->loadTable($dataDevice2);
        $this->loadTable($dataDevice3);
    }
}
