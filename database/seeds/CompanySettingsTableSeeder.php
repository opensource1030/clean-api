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
        while ($i < 21) {
            $data = [
                [
                    'value' => "enable",
                    'name' => 'Bring Your Own Device is allowed',
                    'description' => 'Allow the user to bring his/her own device',
                    'companyId' => $i
                ],
                [
                    'value' => "enable",
                    'name' => 'Pay for Your Device is allowed',
                    'description' => 'Allow the user to pay for his/her device.',
                    'companyId' => $i
                ]
            ];

            $this->loadTable($data);
            $i++;
        }        
    }
}
