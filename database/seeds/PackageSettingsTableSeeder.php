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
            $dataS1 = [
                [
                    'globalSettingsValueId' => rand(7,9),
                    'packageId' => $i // ADMIN
                ]
            ];

            $this->loadTable($dataS1);

            $dataS2 = [
                [
                    'globalSettingsValueId' => rand(10,12),
                    'packageId' => $i // ADMIN
                ]
            ];

            $this->loadTable($dataS2);

            $dataS3 = [
                [
                    'globalSettingsValueId' => rand(13,15),
                    'packageId' => $i // ADMIN
                ]
            ];

            $this->loadTable($dataS3);

            $i++;
        }
    }
}
