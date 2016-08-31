<?php

use Laravel\Lumen\Testing\DatabaseTransactions;

class ModificationsApiTest extends TestCase
{
    use DatabaseTransactions;     
     

    /**
     * A basic functional test for modifications
     *
     *
     */
    public function testGetModifications()
    {       
        $res = $this->json('GET', 'modifications');

        $res->seeJsonStructure([
                'data' => [
                    0 => [ 'type','id',
                        'attributes' => [
                            'type',
                            'value',
                        ],
                        'links'
                    ]
                ]
            ]);
    }

    public function testGetModificationById()
    {
        $modification = factory(\WA\DataStore\Modification\Modification::class)->create();

        $res = $this->json('GET', 'modifications/'.$modification->id)
            ->seeJson([
                'type' => 'modifications',
                'type'=> $modification->type,
                'value'=> $modification->value,
            ]);
    }

    public function testCreateModification()
    {
        $this->post('/modifications',
            [
                'type' => 'Modification Type',
                'value' => 'Modification Value',
            ])
            ->seeJson([
                'type' => 'modifications',
                'type' => 'Modification Type',
                'value' => 'Modification Value',

            ]);
    }

    public function testUpdateModification()
    {
        $modification = factory(\WA\DataStore\Modification\Modification::class)->create();

        $this->put('/modifications/'.$modification->id, [
                'type'=> 'Modification Type Edit',
                'value'=> 'Modification Value Edit',

            ])
            ->seeJson([
                'type' => 'modifications',
                'type'=> 'Modification Type Edit',
                'value'=> 'Modification Value Edit',

            ]);
    }

    public function testDeleteModification()
    {
        $modification = factory(\WA\DataStore\Modification\Modification::class)->create();
        $this->delete('/modifications/'. $modification->id);
        $response = $this->call('GET', '/modifications/'.$modification->id);
        $this->assertEquals(500, $response->status());
    }

}