<?php

/**
 * PackagesTableSeeder - Insert info into database.
 *
 * @author   Agustí Dosaiguas
 */
class PackagesTableSeeder extends BaseTableSeeder
{
    protected $table = "packages";

    /**
     * Run the database seeds.
     */
    public function run()
    {
        $this->deleteTable();

        $data = [

            [
                'name'     => "Package1",
                'companyId' => 1
            ],
            [
                'name'     => "Package2",
                'companyId' => 1
            ],
            [
                'name'     => "Package3",
                'companyId' => 1
            ],
            [
                'name'     => "Package4",
                'companyId' => 1
            ],
            [
                'name'     => "Package5",
                'companyId' => 1
            ],
            [
                'name'     => "Package1",
                'companyId' => 20
            ],
            [
                'name'     => "Package2",
                'companyId' => 20
            ],
            [
                'name'     => "Package3",
                'companyId' => 20
            ],
            [
                'name'     => "Package4",
                'companyId' => 20
            ],
            [
                'name'     => "Package5",
                'companyId' => 20
            ],
            [
                'name'     => "Package6",
                'companyId' => 20
            ],
            [
                'name'     => "Package7",
                'companyId' => 20
            ],
            [
                'name'     => "Package8",
                'companyId' => 20
            ],
            [
                'name'     => "Package9",
                'companyId' => 20
            ],
            [
                'name'     => "Package10",
                'companyId' => 20
            ],
            [
                'name'     => "Package11",
                'companyId' => 20
            ],
            [
                'name'     => "Package12",
                'companyId' => 20
            ],
            [
                'name'     => "Package13",
                'companyId' => 20
            ],
            [
                'name'     => "Package14",
                'companyId' => 20
            ],
            [
                'name'     => "Package15",
                'companyId' => 20
            ],
            [
                'name'     => "Package16",
                'companyId' => 20
            ],
            [
                'name'     => "Package17",
                'companyId' => 20
            ],
            [
                'name'     => "Package18",
                'companyId' => 20
            ],
            [
                'name'     => "Package19",
                'companyId' => 20
            ],
            [
                'name'     => "Package20",
                'companyId' => 20
            ],
            [
                'name'     => "Package21",
                'companyId' => 20
            ],
            [
                'name'     => "Package22",
                'companyId' => 20
            ],
            [
                'name'     => "Package23",
                'companyId' => 20
            ],
            [
                'name'     => "Package24",
                'companyId' => 20
            ],
            [
                'name'     => "Package25",
                'companyId' => 20
            ],
            [
                'name'     => "Package26",
                'companyId' => 20
            ],
            [
                'name'     => "Package27",
                'companyId' => 20
            ],
            [
                'name'     => "Package28",
                'companyId' => 20
            ],
            [
                'name'     => "Package29",
                'companyId' => 20
            ],
            [
                'name'     => "Package30",
                'companyId' => 20
            ],
            [
                'name'     => "Package31",
                'companyId' => 20
            ],
            [
                'name'     => "Package32",
                'companyId' => 20
            ],
            [
                'name'     => "Package33",
                'companyId' => 20
            ],
            [
                'name'     => "Package34",
                'companyId' => 20
            ],
            [
                'name'     => "Package35",
                'companyId' => 20
            ]
        ];

        $this->loadTable($data);
    }
}
