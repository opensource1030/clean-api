<?php

/**
 * CompanySettingsTableSeeder - Insert info into database.
 *
 * @author   Agustí Dosaiguas
 */
class CompanySettingsTableSeeder extends BaseTableSeeder
{
    protected $table = 'company_settings';

    /**
     * Run the database seeds.
     */
    public function run()
    {
        $this->deleteTable();

        $i = 1;
        while ($i < 20) {
            $dataS1 = [
                [
                    'globalSettingsValueId' => rand(1,2),
                    'companyId' => $i // ADMIN
                ]
            ];

            $this->loadTable($dataS1);

            $dataS2 = [
                [
                    'globalSettingsValueId' => rand(3,4),
                    'companyId' => $i // ADMIN
                ]
            ];

            $this->loadTable($dataS2);

            $dataS6 = [
                [
                    'globalSettingsValueId' => rand(11,12),
                    'companyId' => $i // ADMIN
                ]
            ];

            $this->loadTable($dataS6);

            $i++;
        }

    }
}
