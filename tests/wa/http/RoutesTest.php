<?php

namespace WA\Testing\Auth;

use WA\Auth\Login;
use TestCase;

class RoutesTest extends TestCase
{
	public function testApiDoSSOEmail()
	{
		/*
		$ssoAuth = 'WA\Http\Controllers\Auth\SSO';
		$response = $this->call('GET', '/api/doSSO/{email}', $ssoAuth);
		$data = $response->getData();
		echo $data;
		die;

		
    	$api->get('doSSO/{email}', [
        	'as' => 'dosso_login',
        	function ($email) use ($ssoAuth) {
            	$controller = app()->make($ssoAuth);
            	return $controller->loginRequest($email);
        	}
    	]);
    	*/
	}

	public function testApiDoSSOLoginUuid(){
		
		/*
		$api->get('doSSO/login/{uuid}', [
        	'as' => 'dosso',
        	function ($uuid) use ($ssoAuth) {
            	$controller = app()->make($ssoAuth);
            	return $controller->loginUser($uuid);
        	}
    	]);
    	*/
	}
}