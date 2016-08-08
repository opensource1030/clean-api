<?php

namespace WA\Http\Controllers\Api;

use LucaDegasperi\OAuth2Server\Authorizer;
use Illuminate\Contracts\Routing\ResponseFactory as Response;
use WA\Auth\Auth;


/**
 * Class AuthTokenController
 *
 * @package WA\Http\Controllers\Api
 */
class AuthTokenController extends ApiController
{
    public function accessToken(Authorizer $authorizer, Response $response)
    {
        return $response->json($authorizer->issueAccessToken());
    }

    public function verify($username, $password)
    {
        $credentials = [
            'email' => $username,
            'password' => $password,
        ];

        $auth = new Auth();

        if ($auth->login($credentials)) {
            return $auth->user()->id;
        } else {
            return false;
        }


    }
}
