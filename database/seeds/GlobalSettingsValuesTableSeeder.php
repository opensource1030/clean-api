<?php

/**
 * CompanySettingsTableSeeder - Insert info into database.
 *
 * @author   Agustí Dosaiguas
 */
class GlobalSettingsValuesTableSeeder extends BaseTableSeeder
{
    protected $table = 'global_settings_values';

    /**
     * Run the database seeds.
     */
    public function run()
    {
        $this->deleteTable();

        $data = [
            [
                'name' => 'enable',
                'label' => 'Enable',
                'globalSettingId' => 1,
            ],
            [
                'name' => 'disable',
                'label' => 'Disable',
                'globalSettingId' => 1,
            ],
            [
                'name' => 'protected',
                'label' => 'Protected',
                'globalSettingId' => 1,
            ],
            [
                'name' => 'enable',
                'label' => 'Enable',
                'globalSettingId' => 2,
            ],
            [
                'name' => 'disable',
                'label' => 'Disable',
                'globalSettingId' => 2,
            ],
            [
                'name' => 'protected',
                'label' => 'Protected',
                'globalSettingId' => 2,
            ],
        ];

        $this->loadTable($data);
    }
}
