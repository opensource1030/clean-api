<?php

/**
 * PackageConditionsTableSeeder - Insert info into database.
 *
 * @author   Agustí Dosaiguas
 */
class PackageConditionsTableSeeder extends BaseTableSeeder
{
    protected $table = 'package_conditions';

    /**
     * Run the database seeds.
     */
    public function run()
    {
        $this->deleteTable();

        $data = [

            [
                'packageId' => 1,
                'conditionId' => 1,
            ],
            [
                'packageId' => 1,
                'conditionId' => 2,
            ],
            [
                'packageId' => 1,
                'conditionId' => 3,
            ],
            [
                'packageId' => 1,
                'conditionId' => 4,
            ],
            [
                'packageId' => 1,
                'conditionId' => 5,
            ],
            [
                'packageId' => 1,
                'conditionId' => 6,
            ],
            [
                'packageId' => 1,
                'conditionId' => 7,
            ],
            [
                'packageId' => 1,
                'conditionId' => 8,
            ],
            [
                'packageId' => 1,
                'conditionId' => 9,
            ],
            [
                'packageId' => 1,
                'conditionId' => 10,
            ],
            [
                'packageId' => 1,
                'conditionId' => 11,
            ],
        ];

        $this->loadTable($data);
    }
}
