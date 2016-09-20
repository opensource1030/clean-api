<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use WA\DataStore\Modification\Modification;

class ModificationsApiTest extends TestCase
{
    use DatabaseMigrations;     
     

    /**
     * A basic functional test for modifications
     *
     *
     */
    public function testGetModifications()
    {       
        $modification = factory(\WA\DataStore\Modification\Modification::class)->create();

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
                "data" => [
                    "type" => 'modifications',
                    "attributes" => [
                        'type' => 'Modification Type',
                        'value' => 'Modification Value',
                    ]
                ]                
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

        $this->put('/modifications/'.$modification->id, 
            [
                "data" => [
                    "type" => 'modifications',
                    "attributes" => [
                        'type' => 'Modification Type Edit',
                        'value' => 'Modification Value Edit',
                    ]
                ]                
            ])
            ->seeJson([
                'type' => 'modifications',
                'type'=> 'Modification Type Edit',
                'value'=> 'Modification Value Edit',

            ]);
    }

    public function testDeleteModificationIfExists() {

        // CREATE & DELETE
        $modification = factory(\WA\DataStore\Modification\Modification::class)->create();
        $responseDel = $this->call('DELETE', '/modifications/'.$modification->id);
        $this->assertEquals(200, $responseDel->status());
        $responseGet = $this->call('GET', '/modifications/'.$modification->id);
        $this->assertEquals(409, $responseGet->status());        
    }

    public function testDeleteModificationIfNoExists(){

        // DELETE NO EXISTING.
        $responseDel = $this->call('DELETE', '/modifications/1');
        $this->assertEquals(409, $responseDel->status());
    }

}