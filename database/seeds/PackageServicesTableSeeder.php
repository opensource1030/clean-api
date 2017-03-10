<?php

/**
 * PackageServicesTableSeeder - Insert info into database.
 *
 * @author   Agustí Dosaiguas
 */
class PackageServicesTableSeeder extends BaseTableSeeder
{
    protected $table = 'package_services';

    /**
     * Run the database seeds.
     */
    public function run()
    {
        $this->deleteTable();

        $data = [

            [
                'packageId' => 1,
                'serviceId' => 1,
            ],
            [
                'packageId' => 1,
                'serviceId' => 2,
            ],
            [
                'packageId' => 1,
                'serviceId' => 5,
            ],
            [
                'packageId' => 1,
                'serviceId' => 11,
            ],
            [
                'packageId' => 2,
                'serviceId' => 4,
            ],
            [
                'packageId' => 2,
                'serviceId' => 6,
            ],
            [
                'packageId' => 2,
                'serviceId' => 7,
            ],
            [
                'packageId' => 2,
                'serviceId' => 8,
            ],
            [
                'packageId' => 2,
                'serviceId' => 12,
            ],
            [
                'packageId' => 2,
                'serviceId' => 14,
            ],
            [
                'packageId' => 2,
                'serviceId' => 15,
            ],
            [
                'packageId' => 3,
                'serviceId' => 8,
            ],
            [
                'packageId' => 3,
                'serviceId' => 4,
            ],
            [
                'packageId' => 3,
                'serviceId' => 6,
            ],
            [
                'packageId' => 3,
                'serviceId' => 7,
            ],
            [
                'packageId' => 3,
                'serviceId' => 8,
            ],
            [
                'packageId' => 4,
                'serviceId' => 4,
            ],
            [
                'packageId' => 4,
                'serviceId' => 6,
            ],
            [
                'packageId' => 4,
                'serviceId' => 7,
            ],
            [
                'packageId' => 4,
                'serviceId' => 8,
            ],
        ];

        $this->loadTable($data);
    }
}
