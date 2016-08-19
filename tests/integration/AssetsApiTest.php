<?php

use Laravel\Lumen\Testing\DatabaseTransactions;


class AssetsApiTest extends TestCase
{

    use DatabaseTransactions;


    /**
     * A basic functional test for assets endpoints
     *
     *
     */
    public function testGetAssets()
    {

        $asset = factory(\WA\DataStore\Asset\Asset::class)->create();

        $this->get('/assets/')
            ->seeJsonStructure([
                'data' => [
                    0 => [ 'type','id',
                        'attributes' => [
                           'identification' , 'active'
                        ],
                        'links'
                    ]

                ]

            ]);
    }

    public function testGetAssetById()
    {
        $asset = factory(\WA\DataStore\Asset\Asset::class)->create();

        $this->get('/assets/'. $asset->id)
            ->seeJson([
                'type' => 'assets',
                'id'=> "$asset->id",
                'identification' => $asset->identification,
                'active' => $asset->active,
            ]);
    }

}