<?php

/**
 * CompanyUserImportJobTableSeeder - Insert info into database.
 *
 * @author   Agustí Dosaiguas
 */
class CompanyUserImportJobTableSeeder extends BaseTableSeeder
{
    protected $table = 'company_user_import_jobs';

    /**
     * Run the database seeds.
     */
    public function run()
    {
        $this->deleteTable();
        
        factory(\WA\DataStore\Company\CompanyUserImportJob::class, 10)->create();
    }
}
