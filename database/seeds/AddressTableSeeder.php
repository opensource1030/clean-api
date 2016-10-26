<?php

/**
 * AppsTableSeeder - Insert info into database.
 *
 * @author   Agustí Dosaiguas
 */
class AddressTableSeeder extends BaseTableSeeder
{
    protected $table = 'address';

    /**
     * Run the database seeds.
     */
    public function run()
    {
        $this->deleteTable();

        $data = [

            [
                'address' => 'address1',
                'city' => 'city1',
                'state' => 'state1',
                'country' => 'country1',
                'postalCode' => 'postalCode1',
            ],
            [
                'address' => 'address2',
                'city' => 'city2',
                'state' => 'state2',
                'country' => 'country2',
                'postalCode' => 'postalCode2',
            ],
            [
                'address' => 'address3',
                'city' => 'city3',
                'state' => 'state3',
                'country' => 'country3',
                'postalCode' => 'postalCode3',
            ],
            [
                'address' => 'address4',
                'city' => 'city4',
                'state' => 'state4',
                'country' => 'country4',
                'postalCode' => 'postalCode4',
            ],
        ];

        $this->loadTable($data);
    }
}
