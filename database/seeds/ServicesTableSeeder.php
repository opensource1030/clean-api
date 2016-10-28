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
                'cost' => 25,
                'description' => 'Reduces the per minute rate for calls originating from inside the U.S.',
                'domesticMinutes' => 3000,
                'domesticData' => 1000,
                'domesticMessages' => 1000,
                'internationalMinutes' => 300,
                'internationalData' => 100,
                'internationalMessages' => 100,
                'carrierId' => 1,
            ],
            [
                'status' => 'Enabled',
                'title' => 'Pooled International Voice Plan',
                'planCode' => 66666,
                'cost' => 35,
                'description' => 'Reduces the per minute rate for calls originating from outside the U.S.',
                'domesticMinutes' => 300,
                'domesticData' => 100,
                'domesticMessages' => 100,
                'internationalMinutes' => 3000,
                'internationalData' => 1000,
                'internationalMessages' => 1000,
                'carrierId' => 2,
            ],
            [
                'status' => 'Disabled',
                'title' => 'Pooled Domestic Data Plan',
                'planCode' => 77777,
                'cost' => 15,
                'description' => 'Reduces the per minute rate for data originating from inside the U.S.',
                'domesticMinutes' => 1000,
                'domesticData' => 3000,
                'domesticMessages' => 1000,
                'internationalMinutes' => 100,
                'internationalData' => 300,
                'internationalMessages' => 100,
                'carrierId' => 3,
            ],
            [
                'status' => 'Enabled',
                'title' => 'Pooled International Data Plan',
                'planCode' => 88888,
                'cost' => 20,
                'description' => 'Reduces the per minute rate for data originating from outside the U.S.',
                'domesticMinutes' => 100,
                'domesticData' => 300,
                'domesticMessages' => 100,
                'internationalMinutes' => 1000,
                'internationalData' => 3000,
                'internationalMessages' => 1000,
                'carrierId' => 1,
            ],
        ];

        $this->loadTable($data);
    }
}
