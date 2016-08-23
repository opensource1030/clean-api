<?php

namespace WA\Testing\Auth;

use WA\Auth\Login;
use TestCase;
use Cache;

class Saml2ApiTest extends TestCase
{
	public function testApiDoSSOEmailRegister()
	{
		// CREATE ARGUMENTS
		$emailRegister = 'dev@algo.com';
		
        // CALL THE API ROUTE + ASSERTS        
		$returnRegister = $this->json('GET', 'api/doSSO/'.$emailRegister)->seeJson([
			'error' => 'User Not Found, Register Required',
			'message' => 'Please, register a new user.'
		]);
	}

	public function testApiDoSSOEmailPassword()
	{
		// CREATE ARGUMENTS
		$emailPassword = 'dariana.donnelly@example.com';
		
        // CALL THE API ROUTE + ASSERTS        
		$returnPassword = $this->json('GET', 'api/doSSO/'.$emailPassword)->seeJson([
			'error' => 'User Found, Password Required',
			'message' => 'Please, enter your password.'
		]);
	}

	public function testApiDoSSOEmailMicrosoftFail()
	{
		// CREATE ARGUMENTS
		$emailMicrosoft = 'dev@wirelessanalytics.com';
		
        // CALL THE API ROUTE + ASSERTS        
		$returnMicrosoft = $this->call('GET', 'api/doSSO/'.$emailMicrosoft, array(), array(), array(), array(), array());
		$returnMicrosoftArray = json_decode($returnMicrosoft->content(), true);
		// ->json(['error' => 'URL Not Found', 'message' => 'Url to redirect not found.'])->setStatusCode(409);

		$this->assertArrayHasKey('error', $returnMicrosoftArray);
		$this->assertArrayHasKey('message', $returnMicrosoftArray);
		$this->assertStringStartsWith('URL Not Found', $returnMicrosoftArray['error']);
		$this->assertStringStartsWith('Url to redirect not found.', $returnMicrosoftArray['message']);
	}

	public function testApiDoSSOEmailMicrosoftSaml2(){
		$emailMicrosoft = 'dev@wirelessanalytics.com';
		$redirectToUrl = 'http://google.es';

        // CALL THE API ROUTE + ASSERTS        
		$returnMicrosoft = $this->call('GET', 'api/doSSO/'.$emailMicrosoft.'?redirectToUrl='.$redirectToUrl, array(), array(), array(), array(), array());
		$returnMicrosoftArray = json_decode($returnMicrosoft->content(), true);
		$this->assertArrayHasKey('data', $returnMicrosoftArray);
		$this->assertArrayHasKey('redirectUrl', $returnMicrosoftArray['data']);
		$this->assertStringStartsWith('https://login.microsoftonline.com', $returnMicrosoftArray['data']['redirectUrl']);
	}

	public function testApiDoSSOEmailFacebookFail()
	{
		// CREATE ARGUMENTS
		$emailFacebook = 'dev@sharkninja.com';
		
        // CALL THE API ROUTE + ASSERTS        
		$returnFacebook = $this->call('GET', 'api/doSSO/'.$emailFacebook, array(), array(), array(), array(), array());
		$returnFacebookArray = json_decode($returnFacebook->content(), true);
		$this->assertArrayHasKey('error', $returnFacebookArray);
		$this->assertArrayHasKey('message', $returnFacebookArray);
		$this->assertStringStartsWith('URL Not Found', $returnFacebookArray['error']);
		$this->assertStringStartsWith('Url to redirect not found.', $returnFacebookArray['message']);
	}

	public function testApiDoSSOEmailFacebookSaml2(){
		$emailFacebook = 'dev@sharkninja.com';
		$redirectToUrl = 'http://google.es';

        // CALL THE API ROUTE + ASSERTS        
		$returnFacebook = $this->call('GET', 'api/doSSO/'.$emailFacebook.'?redirectToUrl='.$redirectToUrl, array(), array(), array(), array(), array());
		$returnFacebookArray = json_decode($returnFacebook->content(), true);
		$this->assertArrayHasKey('data', $returnFacebookArray);
		$this->assertArrayHasKey('redirectUrl', $returnFacebookArray['data']);
		$this->assertStringStartsWith('http://simplesamlphp.dev/simplesaml/saml2/idp/SSOService', $returnFacebookArray['data']['redirectUrl']);
	}

	public function testApiDoSSOEmailNoValid()
	{
		// CREATE ARGUMENTS
		$emailNoValid = 'dev';

        // CALL THE API ROUTE + ASSERTS        
		$returnNoValid = $this->json('GET', 'api/doSSO/'.$emailNoValid)->seeJson([
			'error' => 'Invalid Email',
			'message' => 'Please, enter a valid Email Address.'
		]);
	}

	public function testApiDoSSOLoginUuid()
	{
		// CREATE ARGUMENTS ERROR
		$uuid = 'siriondevelopers';

		// CALL THE API ROUTE WITHOUT LARAVEL USER -> ERROR
		$returnRegister = $this->json('GET', 'api/doSSO/login/'.$uuid)->seeJson([
			'error' => 'Required User',
			'message' => 'Please, user is not available now, try again later.'
		]);

		// CREATE ARGUMENTS SUCCESS
		$laravelUser['attributes']['id'] = 1;
        Cache::put('saml2user_'.$uuid, $laravelUser, 1);

        // CALL THE API ROUTE + ASSERTS
		$returnRegister = $this->json('GET', 'api/doSSO/login/'.$uuid)->seeJson([
			'success' => 'User Successfully Logged',
			'uuid' => $uuid,
		]);
	}
}