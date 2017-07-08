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
                'forType' => 'companies'
            ],
            [
                'name' => 'pay_for_your_device_is_allowed',
                'label' => 'Pay for Your Device is allowed',
                'description' => 'Allow the user to pay for his/her device.',
                'forType' => 'companies'
            ],
            [
                'name' => 'subsided_device',
                'label' => 'Subsided Device',
                'description' => 'choose a device from a list',
                'forType' => 'packages'
            ],
            [
                'name' => 'pay_by_personal_credit_or_debit_card',
                'label' => 'Pay by Personal Credit or Debit Card',
                'description' => 'choose any device and pay by yourself',
                'forType' => 'packages'
            ],
            [
                'name' => 'bring_your_own_device',
                'label' => 'Bring Your Own Device',
                'description' => 'something special',
                'forType' => 'packages'
            ],
            [
                'name' => 'mobility_central_login',
                'label' => 'Mobility Central SSO',
                'description' => 'Allow SSO Login into Mobility Central',
                'forType' => 'companies'
            ]
        ];

        $this->loadTable($data);
    }
}
