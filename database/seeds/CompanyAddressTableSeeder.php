<?php

class CompanyAddressTableSeeder extends BaseTableSeeder
{
    protected $table = 'company_address';

    public function run()
    {
        $this->deleteTable();
        $i = 1;
        while ($i < 100) {
            $data = [
                [
                    'companyId' => rand(1,5),
                    'addressId' => $i
                ]
            ];

            $this->loadTable($data);
            $i++;
        }
    }
}