<?php

use Laravel\Lumen\Testing\DatabaseMigrations;

use Laravel\Lumen\Testing\DatabaseTransactions;
use WA\Http\Controllers\ApiController;
use WA\DataStore\Device\Device;

class BaseApiTest extends TestCase
{
    use DatabaseMigrations;

    public function testCanCallHome()
    {
        $this->json('GET', '/')
            ->seeJson([
                'app_name' => 'CLEAN Platform',
                'api_version' => 'v1',
                'api_domain' => 'clean.api'
            ]);
    }

    public function testStatusCodes()
    {
        $baseController = new class() extends ApiController{};
        $this->assertSame($baseController->status_codes['ok'], 200);
        $this->assertSame($baseController->status_codes['created'], 201);
        $this->assertSame($baseController->status_codes['accepted'], 202);
        $this->assertSame($baseController->status_codes['createdCI'], 204);
        $this->assertSame($baseController->status_codes['forbidden'], 403);
        $this->assertSame($baseController->status_codes['notexists'], 404);
        $this->assertSame($baseController->status_codes['conflict'], 409);
    }

    public function testIncludeRelationships()
    {
        $device = factory(\WA\DataStore\Device\Device::class)->create();

        $price1 = factory(\WA\DataStore\Price\Price::class)->create(['deviceId' => $device->id])->id;
        $price2 = factory(\WA\DataStore\Price\Price::class)->create(['deviceId' => $device->id])->id;

        $this->json('GET', 'devices/'.$device->id.'/relationships/prices')
            ->seeJsonStructure([
                'data' => [
                    0 => [
                        'type',
                        'id',
                        'attributes' => [],
                        'links' => [
                            'self'
                        ]
                    ]
                ],
                'meta' => [
                    'sort',
                    'filter',
                    'fields',
                    'pagination' => [
                        'total',
                        'count',
                        'per_page',
                        'current_page',
                        'total_pages'
                    ]
                ],
                'links' => [
                    'self',
                    'first',
                    'last'
                ]
            ]);
    }

    public function testIncludeRelationshipsErrors()
    {
        $device = factory(\WA\DataStore\Device\Device::class)->create();

        $price1 = factory(\WA\DataStore\Price\Price::class)->create(['deviceId' => $device->id])->id;
        $price2 = factory(\WA\DataStore\Price\Price::class)->create(['deviceId' => $device->id])->id;

        $this->json('GET', 'notexists/'.$device->id.'/relationships/prices')
            ->seeJson([
                'errors' => [
                    'notexists' => 'the Notexist selected doesn\'t exists'
                ]
            ]);

        $this->json('GET', 'devices/'.$device->id.'/relationships/notexists')
            ->seeJson([
                'errors' => [
                    'devices' => 'the notexists selected doesn\'t exists'
                ]
            ]);

        $deviceId = factory(\WA\DataStore\Device\Device::class)->create()->id;

        $res = $this->json('GET', 'devices/'.$deviceId.'/relationships/prices')
            ->seeJsonStructure([
                'total',
                'per_page',
                'current_page',
                'last_page',
                'next_page_url',
                'prev_page_url',
                'from',
                'to',
                'data'
            ]);

        $idNotExists = $deviceId + 10;

        $this->json('GET', 'devices/'.$idNotExists.'/relationships/prices')
            ->seeJson([
                'errors' => [
                    'devices' => 'the Device selected doesn\'t exists'
                ]
            ]);
    }

    public function testIncludeRelationshipsInformation()
    {
        $device = factory(\WA\DataStore\Device\Device::class)->create();

        $price1 = factory(\WA\DataStore\Price\Price::class)->create(['deviceId' => $device->id])->id;
        $price2 = factory(\WA\DataStore\Price\Price::class)->create(['deviceId' => $device->id])->id;

        $this->json('GET', 'devices/'.$device->id.'/prices')
            ->seeJsonStructure([
                'data' => [
                    0 => [
                        'type',
                        'id',
                        'attributes' => [
                            'priceRetail',
                            'price1',
                            'price2',
                            'priceOwn',
                            'deviceId',
                            'capacityId',
                            'styleId',
                            'carrierId',
                            'companyId',
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
                    'sort',
                    'filter',
                    'fields',
                    'pagination' => [
                        'total',
                        'count',
                        'per_page',
                        'current_page',
                        'total_pages'
                    ]
                ],
                'links' => [
                    'self',
                    'first',
                    'last'
                ]
            ]);
    }

    public function testIncludeRelationshipsInformationErrors()
    {
        $device = factory(\WA\DataStore\Device\Device::class)->create();

        $price1 = factory(\WA\DataStore\Price\Price::class)->create(['deviceId' => $device->id])->id;
        $price2 = factory(\WA\DataStore\Price\Price::class)->create(['deviceId' => $device->id])->id;

        $this->json('GET', 'notexists/'.$device->id.'/prices')
            ->seeJson([
                'errors' => [
                    'notexists' => 'the Notexist selected doesn\'t exists'
                ]
            ]);

        $this->json('GET', 'devices/'.$device->id.'/notexists')
            ->seeJson([
                'errors' => [
                    'devices' => 'the notexists selected doesn\'t exists'
                ]
            ]);

        $deviceId = factory(\WA\DataStore\Device\Device::class)->create()->id;

        $res = $this->json('GET', 'devices/'.$deviceId.'/relationships/prices')
            ->seeJsonStructure(
            [
                'total',
                'per_page',
                'current_page',
                'last_page',
                'next_page_url',
                'prev_page_url',
                'from',
                'to',
                'data'
            ]
        );

        $idNotExists = $deviceId + 10;

        $this->json('GET', 'devices/'.$idNotExists.'/relationships/prices')
            ->seeJson([
                'errors' => [
                    'devices' => 'the Device selected doesn\'t exists'
                ]
            ]);
    }

    public function testIsJsonCorrect() {

 		$type = 'anytype';
        $array = array(
            'data' => [
                'type' => $type,
                'attributes' => [
                    'something' => 1
                ]
            ]
        );

        $baseController = new class() extends ApiController{};
        $result = $baseController->isJsonCorrect($array, $type);
        $this->assertTrue($result);
    }

    public function testIsJsonCorrectnoData() {

    	$type = 'anytype';
    	$array = array(
            'error' => [
                'type' => $type,
                'attributes' => [
                    'something' => 1
                ]
            ]
        );

        $baseController = new class() extends ApiController{};
        $result = $baseController->isJsonCorrect($array, $type);
        $this->assertFalse($result);
    }

    public function testIsJsonCorrectnoType(){

    	$type = 'anytype';
        $array = array(
            'data' => [
                'error' => $type,
                'attributes' => [
                    'something' => 1
                ]
            ]
        );

        $baseController = new class() extends ApiController{};
        $result = $baseController->isJsonCorrect($array, $type);
        $this->assertFalse($result);
    }

    public function testIsJsonCorrectnoTypeValue(){

    	$type = 'anytype';
        $array = array(
            'data' => [
                'type' => 'error',
                'attributes' => [
                    'something' => 1
                ]
            ]
        );

        $baseController = new class() extends ApiController{};
        $result = $baseController->isJsonCorrect($array, $type);
        $this->assertFalse($result);
    }

    public function testIsJsonCorrectnoAttributes(){

    	$type = 'anytype';
        $array = array(
            'data' => [
                'type' => $type,
                'error' => [
                    'something' => 1
                ]
            ]
        );

        $baseController = new class() extends ApiController{};
        $result = $baseController->isJsonCorrect($array, $type);
        $this->assertFalse($result);
    }



    public function testParseJsonToArray(){
  
        $array = array();
        $type = 'anytype';
        for ($i = 1; $i < 5; $i++) {
            $arrayAux = array('type' => $type, 'id' => $i);
            array_push($array, $arrayAux);
        }

        $arrayFinal = [1,2,3,4];
  
        $baseController = new class() extends ApiController{};
        $result = $baseController->parseJsonToArray($array, $type);
        $this->assertSame($result, $arrayFinal);
    }
 
    public function testParseJsonToArrayReturnVoidNoType(){
  
        $array = array();
        $type = 'anytype';
        for ($i = 1; $i < 5; $i++) {
              $arrayAux = array('error' => $type, 'id' => $i);
              array_push($array, $arrayAux);
        }
  
        $arrayFinal = [];
  
        $baseController = new class() extends ApiController{};
        $result = $baseController->parseJsonToArray($array, $type);
        $this->assertSame($result, $arrayFinal);
    } 
  
    public function testParseJsonToArrayReturnVoidNoSameType(){
  
        $array = array();
        $type = 'anytype';
        for ($i = 1; $i < 5; $i++) {
              $arrayAux = array('type' => 'error', 'id' => $i);
              array_push($array, $arrayAux);
        }
  
        $arrayFinal = [];
  
        $baseController = new class() extends ApiController{};
        $result = $baseController->parseJsonToArray($array, $type);
        $this->assertSame($result, $arrayFinal);
    } 
  
    public function testParseJsonToArrayReturnVoidNoId(){
  
        $array = array();
        $type = 'anytype';
        for ($i = 1; $i < 5; $i++) {
              $arrayAux = array('type' => $type, 'error' => $i);
              array_push($array, $arrayAux);
        }
  
        $arrayFinal = [];
  
        $baseController = new class() extends ApiController{};
        $result = $baseController->parseJsonToArray($array, $type);
        $this->assertSame($result, $arrayFinal);
    }
}