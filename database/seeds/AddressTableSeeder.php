<?php

/**
 * AddressTableSeeder - Insert info into database.
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
                'name' => 'Zeus',
                'attn' => 'atentamente',
                'phone' => '+34900020202',
                'address' => 'address1',
                'city' => 'city1',
                'state' => 'state1',
                'country' => 'country1',
                'postalCode' => 'postalCode1',
            ],
            [
                'name' => 'Ares',
                'attn' => 'atentamente',
                'phone' => '+34900020202',
                'address' => 'address2',
                'city' => 'city2',
                'state' => 'state2',
                'country' => 'country2',
                'postalCode' => 'postalCode2',
            ],
            [
                'name' => 'Hades',
                'attn' => 'atentamente',
                'phone' => '+34900020202',
                'address' => 'address3',
                'city' => 'city3',
                'state' => 'state3',
                'country' => 'country3',
                'postalCode' => 'postalCode3',
            ],
            [
                'name' => 'Poseidón',
                'attn' => 'atentamente',
                'phone' => '+34900020202',
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
