<?php

//use Laravel\Lumen\Testing\DatabaseTransactions;
use Laravel\Lumen\Testing\DatabaseMigrations;

use WA\DataStore\Carrier\Carrier;
use WA\DataStore\Location\Location;

class CarriersApiTest extends TestCase
{
    //use DatabaseTransactions;
    use DatabaseMigrations;

    /**
     * A basic functional test for Carriers
     *
     *
     */
    public function testGetCarriers() {

        $carrier = factory(\WA\DataStore\Carrier\Carrier::class)->create();

        $res = $this->json('GET', 'carriers');

        $res->seeJsonStructure([
                'data' => [
                    0 => [ 'type','id',
                        'attributes' => [
                            'name',
                            'presentation',
                            'active',
                            'locationId',
                            'shortName',
                        ],
                        'links'
                    ]
                ]
            ]);
    }

    public function testGetCarrierById() {

        $carrier = factory(\WA\DataStore\Carrier\Carrier::class)->create();

        $res = $this->json('GET', 'carriers/'.$carrier->id)
            ->seeJson([
                'type' => 'carriers',
                'name'=> $carrier->name,
                'presentation'=> $carrier->presentation,
                'active'=> "$carrier->active",
                'locationId'=> "$carrier->locationId",
                'shortName'=> $carrier->shortName,
            ]);
    }

    public function testCreateCarrier() {

        $this->post('/carriers',
            [
                'name'=> 'Carrier Name',
                'presentation'=> 'Carrier Presentation',
                'active'=> 1,
                'locationId'=> 1,
                'shortName'=> 'Carrier ShortName',
            ])
            ->seeJson([
                'type' => 'carriers',
                'name'=> 'Carrier Name',
                'presentation'=> 'Carrier Presentation',
                'active'=> 1,
                'locationId'=> 1,
                'shortName'=> 'Carrier ShortName',

            ]);
    }

    public function testUpdateCarrier() {

        $location = factory(\WA\DataStore\Location\Location::class)->create(); 
        $carrier = factory(\WA\DataStore\Carrier\Carrier::class)->create();

        $this->put('/carriers/'.$carrier->id, [
                'name'=> 'Carrier Name',
                'presentation'=> 'Carrier Presentation',
                'active'=> 1,
                'locationId'=> $location->id,
                'shortName'=> 'Carrier ShortName',

            ])
            ->seeJson([
                'type' => 'carriers',
                'name'=> 'Carrier Name',
                'presentation'=> 'Carrier Presentation',
                'active'=> 1,
                'locationId'=> $location->id,
                'shortName'=> 'Carrier ShortName',

            ]);
    }

    public function testDeleteCarrier() {
        
        $carrier = factory(\WA\DataStore\Carrier\Carrier::class)->create();
        $this->delete('/carriers/'. $carrier->id);
        $response = $this->call('GET', '/carriers/'.$carrier->id);
        $this->assertEquals(500, $response->status());
    }

}