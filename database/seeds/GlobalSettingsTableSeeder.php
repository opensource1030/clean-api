<?php

/**
 * CompanySettingsTableSeeder - Insert info into database.
 *
 * @author   Agustí Dosaiguas
 */
class GlobalSettingsTableSeeder extends BaseTableSeeder
{
    protected $table = 'global_settings';

    /**
     * Run the database seeds.
     */
    public function run()
    {
        $this->deleteTable();

        $data = [
            [
                'name' => 'bring_your_own_device_is_allowed',
                'label' => 'Bring Your Own Device is allowed',
                'description' => 'Allow the user to bring his/her own device',
            ],
            [
                'name' => 'pay_for_your_device_is_allowed',
                'label' => 'Pay for Your Device is allowed',
                'description' => 'Allow the user to pay for his/her device.',
            ]
        ];

        $this->loadTable($data);
    }
}
