<?php

/**
 * DeviceVariationsModificationsTableSeeder - Insert info into database.
 *
 * @author   Agustí Dosaiguas
 */
class DeviceVariationsModificationsTableSeeder extends BaseTableSeeder
{
    protected $table = 'device_variations_modifications';

    /**
     * Run the database seeds.
     */
    public function run()
    {
        $this->deleteTable();

        $data = [

            [
                'deviceVariationId' => 1,
                'modificationId' => 1,
            ],
            [
                'deviceVariationId' => 1,
                'modificationId' => 2,
            ],
            [
                'deviceVariationId' => 1,
                'modificationId' => 3,
            ],
            [
                'deviceVariationId' => 1,
                'modificationId' => 4,
            ],
            [
                'deviceVariationId' => 2,
                'modificationId' => 1,
            ],
            [
                'deviceVariationId' => 2,
                'modificationId' => 2,
            ],
            [
                'deviceVariationId' => 2,
                'modificationId' => 3,
            ],
            [
                'deviceVariationId' => 2,
                'modificationId' => 5,
            ],
                        [
                'deviceVariationId' => 3,
                'modificationId' => 1,
            ],
            [
                'deviceVariationId' => 3,
                'modificationId' => 2,
            ],
            [
                'deviceVariationId' => 3,
                'modificationId' => 5,
            ],
            [
                'deviceVariationId' => 3,
                'modificationId' => 6,
            ],
        ];

        $this->loadTable($data);
    }
}
