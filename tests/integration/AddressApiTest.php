<?php

use Laravel\Lumen\Testing\DatabaseMigrations;

use WA\DataStore\Address\Address;

class AddressApiTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * A basic functional test for Address
     */
    public function testGetAddress() {       
        
        factory(\WA\DataStore\Address\Address::class, 40)->create();

        $this->json('GET', 'address')
            ->seeJsonStructure([
            'data' => [
                0 => [  
                    'type',
                    'id',
                    'attributes' => [
                        'address',
                        'city',
                        'state',
                        'country',
                        'postalCode',
                        'created_at' => [
                            'date',
                            'timezone_type',
                            'timezone'
                        ],
                        'updated_at' => [
                            'date',
                            'timezone_type',
                            'timezone'
                        ]
                    ],
                    'links' => [
                        'self'
                    ]
                ]
            ],
            'meta' => [
                'pagination' => [
                    'total',
                    'count',
                    'per_page',
                    'current_page',
                    'total_pages'
                ]
            ],
            'links' => [
                'self'
            ]
        ]);
    }

    public function testGetAddressById() {

        $address = factory(\WA\DataStore\Address\Address::class)->create();

        $res = $this->json('GET', 'address/'.$address->id)
            ->seeJson([
                'type' => 'address',
                'address' => $address->address,
                'city' => $address->city,
                'state' => $address->state,
                'country' => $address->country,
                'postalCode' => $address->postalCode
            ]);
    }

    public function testCreateAddress() {

        $this->json('POST', 'address',
            [
                'address' => 'addressAddress',
                'city'=> 'addressCity',
                'state'=> 'addressState',
                'country'=> 'addressCountry',
                'postalCode'=> 'addressPostalCode',
            ])
            ->seeJson([
                'type' => 'address',
                'address' => 'addressAddress',
                'city'=> 'addressCity',
                'state'=> 'addressState',
                'country'=> 'addressCountry',
                'postalCode'=> 'addressPostalCode'
            ]);
    }

    public function testUpdateAddress() {

        $address = factory(\WA\DataStore\Address\Address::class)->create();

        $this->json('PUT', 'address/'.$address->id, [
                'address' => 'addressAddressEdit',
                'city'=> 'addressCityEdit',
                'state'=> 'addressStateEdit',
                'country'=> 'addressCountryEdit',
                'postalCode'=> 'addressPostalCodeEdit',
            ])
            ->seeJson([
                'type' => 'address',
                'address' => 'addressAddressEdit',
                'city'=> 'addressCityEdit',
                'state'=> 'addressStateEdit',
                'country'=> 'addressCountryEdit',
                'postalCode'=> 'addressPostalCodeEdit',
            ]);
    }

    public function testDeleteAddressIfExists() {

        // CREATE & DELETE
        $address = factory(\WA\DataStore\Address\Address::class)->create();
        $responseDel = $this->call('DELETE', 'address/'.$address->id);
        $this->assertEquals(200, $responseDel->status());
        $responseGet = $this->call('GET', 'address/'.$address->id);
        $this->assertEquals(409, $responseGet->status());        
    }

    public function testDeleteAddressIfNoExists(){

        // DELETE NO EXISTING.
        $responseDel = $this->call('DELETE', 'address/1');
        $this->assertEquals(409, $responseDel->status());
    }
}