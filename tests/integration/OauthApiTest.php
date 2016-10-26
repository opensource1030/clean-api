<?php

namespace WA\Testing\Auth;

use Laravel\Lumen\Testing\DatabaseMigrations;
use WA\DataStore\User\User;
use TestCase;

class OauthApiTest extends TestCase
{
    use DatabaseMigrations;

    public function testApiOauthAccessToken()
    {
        $this->markTestIncomplete(
              'TODO: needs to be reviewed. AuthController@accessToken gets the right Request, the problem could be in League\oauth2-server\src\AuthorizationServer than can\'t get que Request'
        );

        $emailMicrosoft = 'dev@wirelessanalytics.com';
        $oauth = factory(\WA\DataStore\Oauth\Oauth::class)->create();
        $user = factory(\WA\DataStore\User\User::class)->create(['email' => $emailMicrosoft]);

        $parameters = [
                'grant_type' => 'password',
                'client_id' => $oauth->id,
                'client_secret' => $oauth->secret,
                'username' => $emailMicrosoft,
                'password' => 'user',
            ];
        $headers = [
                'Accept' => 'application/x.v1+json',
            ];

        $this->call('POST', 'oauth/access_token', $parameters);
    }
}
