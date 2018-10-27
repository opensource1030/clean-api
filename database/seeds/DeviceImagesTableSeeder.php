<?php

/**
 * DeviceImagesTableSeeder - Insert info into database.
 *
 * @author   Agustí Dosaiguas
 */
class DeviceImagesTableSeeder extends BaseTableSeeder
{
    protected $table = 'device_images';

    /**
     * Run the database seeds.
     */
    public function run()
    {
        $this->deleteTable();

        $imagesIds = [1,2,3,4,5,9,10];

        $i = 1;
        while ($i < 22) {
            $data = [
                [
                    'deviceId' => $i,
                    'imageId' => $imagesIds[array_rand($imagesIds)]
                ]
            ];

            $this->loadTable($data);
            $i++;
        }
    }
}
