<?php

use Laravel\Lumen\Testing\DatabaseTransactions;
use WA\Http\Controllers\ApiController;

class BaseApiTest extends TestCase
{
    public function testCanCallHome()
    {
        $this->json('GET', '/')
            ->seeJson([
                'app_name' => 'CLEAN Platform',
                'api_version' => 'v1',
                'api_domain' => 'clean.api'
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