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
        ];

        $this->loadTable($data);
    }
}
