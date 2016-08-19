<?php

use Laravel\Lumen\Testing\DatabaseTransactions;


class DevicesApiTest extends TestCase
{

    use DatabaseTransactions;


    /**
     * A basic functional test for assets endpoints
     *
     *
     */
    public function testGetDevices()
    {

        $device = factory(\WA\DataStore\Device\Device::class)->create();

        $this->get('/devices/')
            ->seeJsonStructure([
                'data' => [
                    0 => [ 'type','id',
                        'attributes' => [
                            'identification'
                        ],
                        'links'
                    ]

                ]

            ]);
    }

    public function testGetAssetById()
    {
        $device = factory(\WA\DataStore\Device\Device::class)->create();

        $this->get('/devices/'. $device->id)
            ->seeJson([
                'type' => 'devices',
                'id'=> "$device->id",
                'identification' => $device->identification,

            ]);
    }

}