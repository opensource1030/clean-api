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
                'packageId'     => 1,
                'conditionId'      => 11
            ],
            [
                'packageId'     => 2,
                'conditionId'      => 1
            ],
            [
                'packageId'     => 2,
                'conditionId'      => 2
            ],
            [
                'packageId'     => 2,
                'conditionId'      => 3
            ],
            [
                'packageId'     => 2,
                'conditionId'      => 4
            ],
            [
                'packageId'     => 2,
                'conditionId'      => 5
            ],
            [
                'packageId'     => 2,
                'conditionId'      => 12
            ],
            [
                'packageId'     => 2,
                'conditionId'      => 7
            ],
            [
                'packageId'     => 2,
                'conditionId'      => 8
            ],
            [
                'packageId'     => 2,
                'conditionId'      => 9
            ],
            [
                'packageId'     => 2,
                'conditionId'      => 10
            ],
            [
                'packageId'     => 2,
                'conditionId'      => 11
            ],
            [
                'packageId'     => 3,
                'conditionId'      => 1
            ],
            [
                'packageId'     => 3,
                'conditionId'      => 2
            ],
            [
                'packageId'     => 3,
                'conditionId'      => 3
            ],
            [
                'packageId'     => 3,
                'conditionId'      => 4
            ],
            [
                'packageId'     => 3,
                'conditionId'      => 5
            ],
            [
                'packageId'     => 3,
                'conditionId'      => 13
            ],
            [
                'packageId'     => 3,
                'conditionId'      => 7
            ],
            [
                'packageId'     => 3,
                'conditionId'      => 8
            ],
            [
                'packageId'     => 3,
                'conditionId'      => 9
            ],
            [
                'packageId'     => 3,
                'conditionId'      => 10
            ],
            [
                'packageId'     => 3,
                'conditionId'      => 11
            ],
            [
                'packageId'     => 4,
                'conditionId'      => 1
            ],
            [
                'packageId'     => 4,
                'conditionId'      => 2
            ],
            [
                'packageId'     => 4,
                'conditionId'      => 3
            ],
            [
                'packageId'     => 4,
                'conditionId'      => 4
            ],
            [
                'packageId'     => 4,
                'conditionId'      => 5
            ],
            [
                'packageId'     => 4,
                'conditionId'      => 6
            ],
            [
                'packageId'     => 4,
                'conditionId'      => 7
            ],
            [
                'packageId'     => 4,
                'conditionId'      => 8
            ],
            [
                'packageId'     => 4,
                'conditionId'      => 9
            ],
            [
                'packageId'     => 4,
                'conditionId'      => 10
            ],
            [
                'packageId'     => 4,
                'conditionId'      => 11
            ],
        ];

        $this->loadTable($data);
    }
}
