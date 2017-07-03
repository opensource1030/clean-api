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
        $i = 1;
        $data = [
            [
                'packageId' => 1,
                'serviceId' => 1
            ]
        ];
        $this->loadTable($data);

        while ($i < 900) {
            $data = [
                [
                    'packageId' => rand(2, 39),
                    'serviceId' => rand(2, 900)
                ]
            ];

            $this->loadTable($data);
            $i++;
        }
    }
}
