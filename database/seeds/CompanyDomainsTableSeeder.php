<?php

/**
 * CompanyDomainsTableSeeder - Insert info into database.
 *
 * @author   Agustí Dosaiguas
 */
class CompanyDomainsTableSeeder extends BaseTableSeeder
{
    protected $table = 'company_domains';

    /**
     * Run the database seeds.
     */
    public function run()
    {
        $this->deleteTable();

        $domainMicrosoft = [
            'domain' => 'wirelessanalytics.com',
            'active' => 1,
            'companyId' => 9,
        ];

        $domainNoSSO = [
            'domain' => 'testing.com',
            'active' => 1,
            'companyId' => 20,
        ];

        $this->loadTable($domainMicrosoft);
        $this->loadTable($domainNoSSO);
    }
}
