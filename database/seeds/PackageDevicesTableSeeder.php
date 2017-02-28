<?php

/**
 * PackageDevicesTableSeeder - Insert info into database.
 *
 * @author   Agustí Dosaiguas
 */
class PackageDevicesTableSeeder extends BaseTableSeeder
{
    protected $table = 'package_devices';

    /**
     * Run the database seeds.
     */
    public function run()
    {
        $this->deleteTable();

        $data = [

            [
                'packageId' => 1,
                'deviceVariationId' => 1,
            ],
            [
                'packageId' => 1,
                'deviceVariationId' => 2,
            ],
            [
                'packageId' => 1,
                'deviceVariationId' => 3,
            ],
            [
                'packageId' => 1,
                'deviceVariationId' => 4,
            ],
        ];

        $this->loadTable($data);
    }
}
