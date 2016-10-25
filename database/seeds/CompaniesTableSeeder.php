<?php


class CompaniesTableSeeder extends BaseTableSeeder
{
    protected $table = 'companies';


    public function run()
    {
        $this->deleteTable();

        factory(\WA\DataStore\Company\Company::class, 20)->create();
    }

}
