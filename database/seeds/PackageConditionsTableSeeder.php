<?php

/**
 * PackageConditionsTableSeeder - Insert info into database.
 *  
 * @author   Agustí Dosaiguas
 */

class PackageConditionsTableSeeder extends BaseTableSeeder
{
    protected $table = "package_conditions";

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->deleteTable();

        $data = [

            [
                'packageId'     => 1,
                'conditionsId'      => 1
            ],
            [
                'packageId'     => 1,
                'conditionsId'      => 2
            ],
            [
                'packageId'     => 1,
                'conditionsId'      => 3
            ],
            [
                'packageId'     => 1,
                'conditionsId'      => 4
            ],
            [
                'packageId'     => 1,
                'conditionsId'      => 5
            ],
            [
                'packageId'     => 1,
                'conditionsId'      => 6
            ],
            [
                'packageId'     => 1,
                'conditionsId'      => 7
            ],
            [
                'packageId'     => 1,
                'conditionsId'      => 8
            ],
            [
                'packageId'     => 1,
                'conditionsId'      => 9
            ],
            [
                'packageId'     => 1,
                'conditionsId'      => 10
            ],
            [
                'packageId'     => 1,
                'conditionsId'      => 11
            ]
        ];

        $this->loadTable($data);
    }
}