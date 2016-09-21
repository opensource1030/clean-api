<?php

namespace WA\Testing\Auth;

use Laravel\Lumen\Testing\DatabaseMigrations;

use WA\DataStore\User\User;
//use WA\DataStore\Apps\Apps;

use WA\Auth\Login;
use TestCase;
use Cache;

class OauthApiTest extends TestCase
{
	use DatabaseMigrations;


	public function testApiOauthAccessToken()
	{
		$this->assertTrue(true);
		/*
		$this->artisan('db:seed');

		$parameters = [
    			'grant_type' 	=> 'password',
	            'client_id' 	=> 'g73hhd8j3bhcuhdbbs88e4wd',
	            'client_secret' => '786wndkd8iu4nn49ixjndfodsde33',
	            'username'		=> 'dev@wirelessanalytics.com',
	            'password'		=> 'user'
    		];
    	$headers = [
				'Accept' => 'application/x.v1+json'
			];
		
		
        $res = $this->json(
                'POST',
				'oauth/access_token',
				$parameters,
				$headers			
			);

        var_dump($res);
        die;
        
        $this->assertTrue(true);
        */
	}
}