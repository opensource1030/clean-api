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
        $grantType = 'password';
        $password = 'user';

        $user = factory(\WA\DataStore\User\User::class)->create([
            'email' => 'email@email.wirelessanalytics.com',
            'password' => '$2y$10$oc9QZeaYYAd.8BPGmXGaFu9cAqycKTcBu7LRzmT2J231F0BzKwpxj6'
        ]);

        $oauth = factory(\WA\DataStore\Oauth\Oauth::class)->create([
            'user_Id' => null,
            'name' => 'Password Grant Client',
            'secret' => 'ab9QdKGBXZmZn50aPlf4bLlJtC4BJJNC0M99i7B7',
            'redirect' => 'http://localhost',
            'personal_access_client' => 0,
            'password_client' => 1,
            'revoked' => 0,
        ]);

        $body = [
            'grant_type' => $grantType,
            'username' => $user->email,
            'password' => $password,
            'client_id' => $oauth->id,
            'client_secret' => $oauth->secret
        ];

        $res = $this->call('POST', 'oauth/token', $body, [], [], [], true );
        $array = (array)json_decode($res->getContent());

        $this->assertArrayHasKey('user_id', $array);
        $this->assertArrayHasKey('token_type', $array);
        $this->assertArrayHasKey('expires_in', $array);
        $this->assertArrayHasKey('access_token', $array);
        $this->assertArrayHasKey('refresh_token', $array);

        $this->assertEquals($array['user_id'], '1');
        $this->assertEquals($array['token_type'], 'Bearer');
        $this->assertEquals(strlen($array['access_token']), 1071);
        $this->assertEquals(strlen($array['refresh_token']), 684);
    }
}
