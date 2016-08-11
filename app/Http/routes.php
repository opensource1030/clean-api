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

$app->get('/', function () use ($app) {
    return $app->version();
});

$app->get('doSSO/login/{uuid}', [ 'as' => 'dosso', function($uuid) use ($app) {
    $controller = $app->make('WA\Http\Controllers\Auth\SSO');
    return $controller->loginUser($uuid);
}]);
$app->get('doSSO/{email}', ['as' => 'dosso_login', function($email) use ($app) {
    $controller = $app->make('WA\Http\Controllers\Auth\SSO');
    return $controller->loginRequest($email);
}]);

/*************************
 * =API
 *************************/
$api = $app['api.router'];
/*
$api->version('v1', function ($api) {
    $api->resource('user/access_token', 'WA\Http\Controllers\Api\AuthTokenController@access_token');
});


*/

$api->version('v1', function ($api) {

    $api->post(
        'user/access_token', [
            'as' => 'api.token', 'uses' => 'WA\Http\Controllers\Api\AuthTokenController@accessToken'
        ]
    );

    /*
    $apiAuthController = 'WA\Http\Controllers\Api\AuthTokenController';
    $api->post('user/access_token', ['as' => 'api.token', 'uses' => $apiAuthController . '@accessToken']);
    */

//    $api->group(['middleware' => 'api.auth'], function ($api) {
//
    /*
    // =Companies
    $companiesController = 'WA\Http\Controllers\Api\CompaniesController';

    $api->get('companies', ['as' => 'api.company.index', 'uses' => $companiesController . '@index']);
    $api->get('companies/{id}', ['as' => 'api.company.show', 'uses' => $companiesController . '@show']);

    // =Employees
    $employeesController = 'WA\Http\Controllers\Api\EmployeesController';
    $api->get('employees', ['as' => 'api.employees.index', 'uses' => $employeesController . '@index']);
    $api->get('employees/{id}', ['as' => 'api.employees.show', 'uses' => $employeesController . '@show']);

    // =Assets
    $assetsController = 'WA\Http\Controllers\Api\AssetsController';
    $api->get('assets', ['as' => 'assets.index', 'uses' => $assetsController . '@index']);
    $api->get('assets/{id}', ['as' => 'assets.index', 'uses' => $assetsController . '@show']);

    // =Devices
    $devicesController = 'WA\Http\Controllers\Api\DevicesController';
    $api->get('devices', ['as' => 'api.devices.index', 'uses' => $devicesController . '@index']);
    $api->get('devices/{id}', ['as' => 'api.devices.show', 'uses' => $devicesController . '@show']);

    // =Allocations
    $allocationsController = 'WA\Http\Controllers\Api\AllocationsController';
    $api->get('allocations', ['as' => 'api.allocations.index', 'uses' => $allocationsController . '@index']);
    $api->get('allocations/{id}', ['as' => 'api.allocations.show', 'uses' => $allocationsController . '@show']);

    //=Pages
    $pagesController = 'WA\Http\Controllers\Api\PagesController';
    $api->get('pages', ['as' => 'api.pages.index', 'uses' => $pagesController. '@index']);
    $api->get('pages/{id}', ['as' => 'api.pages.show', 'uses'=> $pagesController. '@show']);
    $api->post('pages', ['uses' => $pagesController. '@store']);
    */

//    });
});