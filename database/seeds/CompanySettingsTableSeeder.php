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

        $data = [
            [
                'globalSettingId' => 1,
                'companyId' => 1,
            ],
            [
                'globalSettingId' => 1,
                'companyId' => 2,
            ],
            [
                'globalSettingId' => 1,
                'companyId' => 3,
            ],
            [
                'globalSettingId' => 2,
                'companyId' => 3,
            ],
            [
                'globalSettingId' => 2,
                'companyId' => 2,
            ],
            [
                'globalSettingId' => 2,
                'companyId' => 1,
            ],
        ];

        $this->loadTable($data);
    }
}
