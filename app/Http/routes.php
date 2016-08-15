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

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) {

    $api->get('/', function(){
        return response()->json([
            'app_name' => 'clean',
            'app_version' => 'v4.0.0',
            'api_version' => 'v1.0.0'
            ]);
    });

    $ssoAuth = 'WA\Http\Controllers\Auth\SSO';
    $api->get('doSSO/{email}', [
        'as' => 'dosso_login',
        function ($email) use ($ssoAuth) {
            $controller = app()->make($ssoAuth);
            return $controller->loginRequest($email);
        }
    ]);

    $api->get('doSSO/login/{uuid}', [
        'as' => 'dosso',
        function ($uuid) use ($ssoAuth) {
            $controller = app()->make($ssoAuth);
            return $controller->loginUser($uuid);
        }
    ]);

    $apiAuth = 'WA\Http\Controllers\Auth\AuthController';
    $api->post('oauth/access_token', ['as' => 'api.token', 'uses' => $apiAuth . '@accessToken']);


    $api->group(['middleware' => 'api.auth'], function ($api) {

        // =Companies
        $companiesController = 'WA\Http\Controllers\CompaniesController';
        $api->get('companies', ['as' => 'api.company.index', 'uses' => $companiesController . '@index']);
        $api->get('companies/{id}', ['as' => 'api.company.show', 'uses' => $companiesController . '@show']);

        // =Employees
        $employeesController = 'WA\Http\Controllers\UsersController';
        $api->get('users', ['as' => 'api.employees.index', 'uses' => $employeesController . '@index']);
        $api->get('users/{id}', ['as' => 'api.employees.show', 'uses' => $employeesController . '@show']);


        // =Assets
        $assetsController = 'WA\Http\Controllers\AssetsController';
        $api->get('assets', ['as' => 'assets.index', 'uses' => $assetsController . '@index']);
        $api->get('assets/{id}', ['as' => 'assets.index', 'uses' => $assetsController . '@show']);


        // =Devices
        $devicesController = 'WA\Http\Controllers\DevicesController';
        $api->get('devices', ['as' => 'api.devices.index', 'uses' => $devicesController . '@index']);
        $api->get('devices/{id}', ['as' => 'api.devices.show', 'uses' => $devicesController . '@show']);


        // =Allocations
        $allocationsController = 'WA\Http\Controllers\AllocationsController';
        $api->get('allocations', ['as' => 'api.allocations.index', 'uses' => $allocationsController . '@index']);
        $api->get('allocations/{id}', ['as' => 'api.allocations.show', 'uses' => $allocationsController . '@show']);

        //=Pages
        $pagesController = 'WA\Http\Controllers\PagesController';
        $api->get('pages', ['as' => 'api.pages.index', 'uses' => $pagesController. '@index']);
        $api->get('pages/{id}', ['as' => 'api.pages.show', 'uses'=> $pagesController. '@show']);
        $api->post('pages', ['uses' => $pagesController. '@create']);
        $api->put('pages/{id}', ['uses' => $pagesController. '@store']);
        $api->delete('pages/{id}', ['uses' => $pagesController . '@deletePage']);


    });
});
