<?php

/**
 * CompanySettingsTableSeeder - Insert info into database.
 *
 * @author   Agustí Dosaiguas
 */
class PackageSettingsTableSeeder extends BaseTableSeeder
{
    protected $table = 'package_settings';

    /**
     * Run the database seeds.
     */
    public function run()
    {
        $this->deleteTable();

        $i = 1;
        while ($i < 41) {
            $dataS3 = [
                [
                    'globalSettingsValueId' => rand(5,6),
                    'packageId' => $i // ADMIN
                ]
            ];

            $this->loadTable($dataS3);

            $dataS4 = [
                [
                    'globalSettingsValueId' => rand(7,8),
                    'packageId' => $i // ADMIN
                ]
            ];

            $this->loadTable($dataS4);

            $dataS5 = [
                [
                    'globalSettingsValueId' => rand(9,10),
                    'packageId' => $i // ADMIN
                ]
            ];

            $this->loadTable($dataS5);

            $i++;
        }
    }
}
