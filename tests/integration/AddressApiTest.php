<?php

use Laravel\Lumen\Testing\DatabaseMigrations;

class AddressApiTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * A basic functional test for Address.
     */
    public function testGetAddress()
    {
        factory(\WA\DataStore\Address\Address::class, 40)->create();

        $this->json('GET', 'address')
            ->seeJsonStructure([
                'data' => [
                    0 => [
                        'type',
                        'id',
                        'attributes' => [
                            'name',
                            'attn',
                            'phone',
                            'address',
                            'city',
                            'state',
                            'country',
                            'postalCode',
                            'created_at' => [
                                'date',
                                'timezone_type',
                                'timezone',
                            ],
                            'updated_at' => [
                                'date',
                                'timezone_type',
                                'timezone',
                            ],
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                ],
                'meta' => [
                    'pagination' => [
                        'total',
                        'count',
                        'per_page',
                        'current_page',
                        'total_pages',
                    ],
                ],
                'links' => [
                    'self',
                ],
            ]);
    }

    public function testGetAddressById()
    {
        $address = factory(\WA\DataStore\Address\Address::class)->create();

        $res = $this->json('GET', 'address/'.$address->id)
            ->seeJson([
                'type' => 'address',
                'name' => $address->name,
                'attn' => $address->attn,
                'phone' => "$address->phone",
                'address' => $address->address,
                'city' => $address->city,
                'state' => $address->state,
                'country' => $address->country,
                'postalCode' => "$address->postalCode",
            ]);
    }

    public function testCreateAddress()
    {
        $this->json('POST', 'address',
            [
                'data' => [
                    'type' => 'address',
                    'attributes' => [
                        'name' => 'addressName',
                        'attn' => 'addressAttn',
                        'phone' => 'addressPhone',
                        'address' => 'addressAddress',
                        'city' => 'addressCity',
                        'state' => 'addressState',
                        'country' => 'addressCountry',
                        'postalCode' => 'addressPostalCode',
                    ],
                ],
            ])
            ->seeJson([
                'type' => 'address',
                'name' => 'addressName',
                'attn' => 'addressAttn',
                'phone' => 'addressPhone',                
                'address' => 'addressAddress',
                'city' => 'addressCity',
                'state' => 'addressState',
                'country' => 'addressCountry',
                'postalCode' => 'addressPostalCode',
            ]);
    }

    public function testUpdateAddress()
    {
        $address1 = factory(\WA\DataStore\Address\Address::class)->create();
        $address2 = factory(\WA\DataStore\Address\Address::class)->create();

        $this->assertNotEquals($address1->id, $address2->id);
        $this->assertNotEquals($address1->address, $address2->address);
        $this->assertNotEquals($address1->city, $address2->city);
        $this->assertNotEquals($address1->state, $address2->state);
        $this->assertNotEquals($address1->country, $address2->country);
        $this->assertNotEquals($address1->postalCode, $address2->postalCode);

        $this->json('GET', 'address/'.$address1->id)
            ->seeJson([
                'type' => 'address',
                'name' => $address1->name,
                'attn' => $address1->attn,
                'phone' => "$address1->phone",
                'address' => $address1->address,
                'city' => $address1->city,
                'state' => $address1->state,
                'country' => $address1->country,
                'postalCode' => "$address1->postalCode",
            ]);

        $this->json('PATCH', 'address/'.$address1->id,
            [
                'data' => [
                    'type' => 'address',
                    'attributes' => [
                        'name' => $address2->name,
                        'attn' => $address2->attn,
                        'phone' => $address2->phone,
                        'address' => $address2->address,
                        'city' => $address2->city,
                        'state' => $address2->state,
                        'country' => $address2->country,
                        'postalCode' => $address2->postalCode,
                    ],
                ],
            ])
            ->seeJson([
                //'type' => 'address',
                'id' => $address1->id,
                'name' => $address2->name,
                'attn' => $address2->attn,
                'phone' => $address2->phone,
                'address' => $address2->address,
                'city' => $address2->city,
                'state' => $address2->state,
                'country' => $address2->country,
                'postalCode' => $address2->postalCode,
            ]);
    }

    public function testDeleteAddressIfExists()
    {
        $address = factory(\WA\DataStore\Address\Address::class)->create();
        $responseDel = $this->call('DELETE', 'address/'.$address->id);
        $this->assertEquals(200, $responseDel->status());
        $responseGet = $this->call('GET', 'address/'.$address->id);
        $this->assertEquals(404, $responseGet->status());
    }

    public function testDeleteAddressIfNoExists()
    {
        $responseDel = $this->call('DELETE', 'address/1');
        $this->assertEquals(404, $responseDel->status());
    }
}
