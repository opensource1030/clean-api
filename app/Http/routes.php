<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

//$app->get('/', function () use ($app) {
//    return $app->version();
//});

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) {

    $ssoAuth = 'WA\Http\Controllers\Auth\SSO';
    $app->get('doSSO/{email}', ['as' => 'dosso_login', function($email) use ($app, $ssoAuth) {
        $controller = $app->make($ssoAuth);
        return $controller->loginRequest($email);
    }]);

    $app->get('doSSO/login/{uuid}', [ 'as' => 'dosso', function($uuid) use ($app, $ssoAuth) {
        $controller = $app->make($ssoAuth);   
        return $controller->loginUser($uuid);
    }]);

    $apiAuth = 'WA\Http\Controllers\Auth\AuthController';
    $api->post('oauth/access_token', ['as' => 'api.token', 'uses' => $apiAuth . '@accessToken']);


    $api->group(['middleware' => 'api.auth'], function ($api) {

    });
});

