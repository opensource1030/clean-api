<?php

/**
 * ServicesTableSeeder - Insert info into database.
 *
 * @author   Agustí Dosaiguas
 */
class ServicesTableSeeder extends BaseTableSeeder
{
    protected $table = 'services';

    /**
     * Run the database seeds.
     */
    public function run()
    {
        $this->deleteTable();

        $data = [

            [
                'status' => 'Enabled',
                'title' => 'Pooled Domestic Voice Plan',
                'planCode' => 55555,
                'cost' => 70,
                'currency' => 'USD',
                'carrierId' => 1,
            ],
            [
                'status' => 'Enabled',
                'title' => 'Pooled International Voice Plan',
                'planCode' => 66666,
                'cost' => 80,
                'currency' => 'GBD',
                'carrierId' => 2,
            ],
            [
                'status' => 'Disabled',
                'title' => 'Pooled Domestic Data Plan',
                'planCode' => 77777,
                'cost' => 85,
                'currency' => 'EUR',
                'carrierId' => 3,
            ]
        ];

        $this->loadTable($data);
    }
}
