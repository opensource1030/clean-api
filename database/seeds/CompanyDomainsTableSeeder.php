<?php

/**
 * CompanyDomainsTableSeeder - Insert info into database.
 *  
 * @author   Agustí Dosaiguas
 */

class CompanyDomainsTableSeeder extends BaseTableSeeder
{
    protected $table = "company_domains";

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->deleteTable();

        $domainMicrosoft = [
            'domain' => 'wirelessanalytics.com',
            'active' => 1,
            'companyId' => 9
        ];

        $this->loadTable($domainMicrosoft);
    }
}