<?php

/**
 * CarrierImagesTableSeeder - Insert info into database.
 *
 * @author   Agustí Dosaiguas
 */
class CarrierImagesTableSeeder extends BaseTableSeeder
{
    protected $table = 'carrier_images';

    /**
     * Run the database seeds.
     */
    public function run()
    {
        $this->deleteTable();

        $data = [

            [
                'carrierId' => 1,
                'imageId' => 5,
            ],
            [
                'carrierId' => 2,
                'imageId' => 6,
            ],
            [
                'carrierId' => 5,
                'imageId' => 7,
            ],
            [
                'carrierId' => 11,
                'imageId' => 8,
            ],
        ];

        $this->loadTable($data);
    }
}
