<?php

namespace WA\Testing\Auth;

//use Laravel\Lumen\Testing\DatabaseTransactions;
use Laravel\Lumen\Testing\DatabaseMigrations;

use WA\Auth\Login;
use TestCase;
use Cache;

class Saml2ApiTest extends TestCase
{
    //use DatabaseTransactions;
    use DatabaseMigrations;    

	public function testApiDoSSOEmailRegister()	{
		
		// CREATE ARGUMENTS
		$emailRegister = 'dev@algo.com';
		
        // CALL THE API ROUTE + ASSERTS        
		$returnRegister = $this->json('GET', 'doSSO/'.$emailRegister)->seeJson([
			'error' => 'User Not Found, Register Required',
			'message' => 'Please, register a new user.'
		]);
	}


	public function testApiDoSSOEmailPassword() {
		
		// CREATE USER
		$user = factory(\WA\DataStore\User\User::class)->create();
		// GET EMAIL
		$parsed = $this->getErrorAndParse($user, 'attributes');

        // CALL THE API ROUTE + ASSERTS        
		$returnPassword = $this->json('GET', 'doSSO/'.$parsed['email'])->seeJson([
			'error' => 'User Found, Password Required',
			'message' => 'Please, enter your password.'
		]);
	}

	public function testApiDoSSOEmailMicrosoftFail() {

		$this->markTestIncomplete(
          'TODO: needs to be reviewed.' 
        );

		$this->artisan('db:seed');

		$emailMicrosoft = 'dev@wirelessanalytics.com';
		
        // CALL THE API ROUTE + ASSERTS        
		$returnMicrosoft = $this->call('GET', 'doSSO/'.$emailMicrosoft, array(), array(), array(), array(), array());
		$returnMicrosoftArray = json_decode($returnMicrosoft->content(), true);
		// ->json(['error' => 'URL Not Found', 'message' => 'Url to redirect not found.'])->setStatusCode(409);

		$this->assertArrayHasKey('error', $returnMicrosoftArray);
		$this->assertArrayHasKey('message', $returnMicrosoftArray);
		$this->assertStringStartsWith('URL Not Found', $returnMicrosoftArray['error']);
		$this->assertStringStartsWith('Url to redirect not found.', $returnMicrosoftArray['message']);

	}

	public function testApiDoSSOEmailMicrosoftSaml2(){

		$this->markTestIncomplete(
          'TODO: needs to be reviewed.' 
        );

		$this->artisan('db:seed');

		// CREATE ARGUMENTS
		$emailMicrosoft = 'dev@wirelessanalytics.com';
		$redirectToUrl = 'http://google.es';

        // CALL THE API ROUTE + ASSERTS        
		$returnMicrosoft = $this->call('GET', 'doSSO/'.$emailMicrosoft.'?redirectToUrl='.$redirectToUrl, array(), array(), array(), array(), array());
		$returnMicrosoftArray = json_decode($returnMicrosoft->content(), true);
		$this->assertArrayHasKey('data', $returnMicrosoftArray);
		$this->assertArrayHasKey('redirectUrl', $returnMicrosoftArray['data']);
		$this->assertStringStartsWith('https://login.microsoftonline.com', $returnMicrosoftArray['data']['redirectUrl']);
	}


	public function testApiDoSSOEmailNoValid() {
		
		// CREATE ARGUMENTS
		$emailNoValid = 'dev';

        // CALL THE API ROUTE + ASSERTS        
		$returnNoValid = $this->json('GET', 'doSSO/'.$emailNoValid)->seeJson([
			'error' => 'Invalid Email',
			'message' => 'Please, enter a valid Email Address.'
		]);
	}

	public function testApiDoSSOLoginUuid()	{
		
		// CREATE ARGUMENTS ERROR
		$uuid = 'siriondevelopers';

		// CALL THE API ROUTE WITHOUT LARAVEL USER -> ERROR
		$returnRegister = $this->json('GET', 'doSSO/login/'.$uuid)->seeJson([
			'error' => 'Required User',
			'message' => 'Please, user is not available now, try again later.'
		]);

		// CREATE ARGUMENTS SUCCESS
		$laravelUser['attributes']['id'] = 1;
        Cache::put('saml2user_'.$uuid, $laravelUser, 1);

        // CALL THE API ROUTE + ASSERTS
		$returnRegister = $this->json('GET', 'doSSO/login/'.$uuid)->seeJson([
			'success' => 'User Successfully Logged',
			'uuid' => $uuid,
		]);
	}


    /*
     *      Transforms an Object and gets the value of a protected variable
     *
     *      @param: 
     *          \Exception $e
     *      @return:
     *          $object->getValue($value);
     */
    private function getErrorAndParse($object, $value) {
        
        try{
            $reflectorResponse = new \ReflectionClass($object);

            $classResponse = $reflectorResponse->getProperty($value);
            $classResponse->setAccessible(true);
            $dataResponse = $classResponse->getValue($object);
            return $dataResponse;    

        } catch (\Exception $e){
            return 'Generic Error';
        }
    }
}