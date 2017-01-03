<?php


class DeviceTypeSeeder extends BaseTableSeeder
{
    protected $table = 'device_types';

    public function run()
    {
        $this->deleteTable();

        //factory(\WA\DataStore\Device\Device::class, 40)->create();
        

                    $dataDeviceType = [

            
            [
                        'name' => 'Smartphone',
                        'statusId' => 1,
            ],
            [
                        'name' => 'Smartphone',
                        'statusId' => 1,
            ],
             [
                        'name' => 'Smartphone',
                        'statusId' => 1,
            ],
             [
                        'name' => 'Smartphone',
                        'statusId' => 1,
            ],
             [
                        'name' => 'Smartphone',
                        'statusId' => 1,
            ],
             [
                        'name' => 'Smartphone',
                        'statusId' => 1,
            ],
             [
                        'name' => 'Smartphone',
                        'statusId' => 1,
            ],
             [
                        'name' => 'Smartphone',
                        'statusId' => 1,
            ],
             [
                        'name' => 'Smartphone',
                        'statusId' => 1,
            ],
             [
                        'name' => 'HEADSET',
                        'statusId' => 1,
            ],

        ];
        $this->loadTable($dataDeviceType);
    }
}
