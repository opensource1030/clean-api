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
        while ($i < 5) {
            $dataS1 = [
                [
                    'globalSettingsValueId' => rand(1,3),
                    'companyId' => $i // ADMIN
                ]
            ];

            $this->loadTable($dataS1);

            $dataS2 = [
                [
                    'globalSettingsValueId' => rand(4,6),
                    'companyId' => $i // ADMIN
                ]
            ];

            $this->loadTable($dataS2);

            $i++;
        }

    }
}
