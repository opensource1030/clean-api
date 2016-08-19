<?php

namespace WA\Testing\Auth;

use WA\Auth\Login;
use TestCase;
use Cache;

class OauthApiTest extends TestCase
{
	public function testApiOauthAccessToken()
	{
		//$apiAuth = 'WA\Http\Controllers\Auth\AuthController';
    	//$api->post('oauth/access_token', ['as' => 'api.token', 'uses' => $apiAuth . '@accessToken']);
    	$parameters = array(
    			'grant_type' 	=> 'sso',
	            'client_id' 	=> 'f3d259ddd3ed8ff3843839b',
	            'client_secret' => '4c7f6f8fa93d59c45502c0ae8c4a95b',
	            'uuid' 			=> 'uuidcodegeneratedfortestingpurposes'
    		);
    	$headers = array(
				'Accept' => 'application/x.v1+json'
			);
		
		$return = $this->post(
				'api/oauth/access_token', 
				$parameters,
				$headers			
			);

        var_dump($return);

		
		/*

    	$result = $this->post('api/oauth/access_token',
            [
                'grant_type' => 'sso',
                'client_id' => 'f3d259ddd3ed8ff3843839b',
                'client_secret' => '4c7f6f8fa93d59c45502c0ae8c4a95b',
                'uuid' => 'uuidcodegeneratedfortestingpurposes',
            ]);

            ->seeJson([
                'token' => '',
                'bearer' => '',
                'otro' => '',
            ]);
        */
	}
}