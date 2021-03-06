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
                'name' => 'enable',
                'label' => 'Enable',
                'globalSettingId' => 3,
            ],
            [
                'name' => 'disable',
                'label' => 'Disable',
                'globalSettingId' => 3,
            ],
            [
                'name' => 'enable',
                'label' => 'Enable',
                'globalSettingId' => 4,
            ],
            [
                'name' => 'disable',
                'label' => 'Disable',
                'globalSettingId' => 4,
            ],
            [
                'name' => 'enable',
                'label' => 'Enable',
                'globalSettingId' => 5,
            ],
            [
                'name' => 'disable',
                'label' => 'Disable',
                'globalSettingId' => 5,
            ],
            [
                'name' => 'enable',
                'label' => 'Enable',
                'globalSettingId' => 6,
            ],
            [
                'name' => 'disable',
                'label' => 'Disable',
                'globalSettingId' => 6,
            ]
        ];

        $this->loadTable($data);
    }
}
