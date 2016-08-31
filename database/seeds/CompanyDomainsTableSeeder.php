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

        $domainFacebook = [
        'domain' => 'sharkninja.com',
        'active' => 1,
        'companyId' => 21
        ];

        $domainMicrosoft = [
            'domain' => 'wirelessanalytics.com',
            'active' => 1,
            'companyId' => 9
        ];

        $this->loadTable($domainFacebook);
        $this->loadTable($domainMicrosoft);
    }
}