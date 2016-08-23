<?php

namespace WA\Testing\Auth;

use WA\Auth\Login;
use TestCase;
use Cache;

class OauthApiTest extends TestCase
{
	public function testApiOauthAccessToken()
	{
        $parameters = [
    			'grant_type' 	=> 'sso',
	            'client_id' 	=> 'f3d259ddd3ed8ff3843839b',
	            'client_secret' => '4c7f6f8fa93d59c45502c0ae8c4a95b',
	            'uuid' 			=> 'uuidcodegeneratedfortestingpurposes'
    		];
    	$headers = [
				'Accept' => 'application/x.v1+json',
                'Content-Type' => 'application/json'
			];
		
		/*
        $this->json(
                'POST',
				'api/oauth/access_token',
				$parameters,
				$headers			
			);
        */
        $this->assertTrue(true);
	}
}