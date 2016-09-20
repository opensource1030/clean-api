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

    $api->get('/', function () {
        return response()->json([
            'app_name' => env('API_NAME','clean'),
            'api_version' => env('API_VERSION', 'v1'),
            'api_domain' => env('API_DOMAIN','clean.api')
        ]);
    });

    $ssoAuth = 'WA\Http\Controllers\Auth\SSO';
    $api->get('doSSO/{email}', [
        'as' => 'dosso_login',
        'uses' => $ssoAuth.'@loginRequest'
    ]);

    $api->get('doSSO/login/{uuid}', [
        'as' => 'dosso',
        'uses' => $ssoAuth.'@loginUser'
    ]);

    /*
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
    */

    $apiAuth = 'WA\Http\Controllers\Auth\AuthController';
    $api->post('oauth/access_token', ['as' => 'api.token', 'uses' => $apiAuth . '@accessToken']);

    $middleware = [ ];
    if ( !app()->runningUnitTests() ) {
        if (env('API_AUTH_MIDDLEWARE')!==null) {
            $middleware[] = env('API_AUTH_MIDDLEWARE', 'api.auth');
        }
    }


    $api->group(['middleware' => $middleware ], function ($api) {

        // =Companies
        $companiesController = 'WA\Http\Controllers\CompaniesController';
        $api->get('companies', ['as' => 'api.company.index', 'uses' => $companiesController . '@index']);
        $api->get('companies/{id}', ['as' => 'api.company.show', 'uses' => $companiesController . '@show']);
        $api->post('companies', ['uses'=> $companiesController . '@create']);
        $api->put('companies/{id}', ['uses' => $companiesController . '@store']);
        $api->delete('companies/{id}', ['uses' => $companiesController. '@deleteCompany']);

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
        $api->post('devices', ['uses' => $devicesController . '@create']);
        $api->put('devices/{id}', ['uses' => $devicesController . '@store']);
        $api->delete('devices/{id}', ['uses' => $devicesController . '@delete']);


        // =Allocations
        $allocationsController = 'WA\Http\Controllers\AllocationsController';
        $api->get('allocations', ['as' => 'api.allocations.index', 'uses' => $allocationsController . '@index']);
        $api->get('allocations/{id}', ['as' => 'api.allocations.show', 'uses' => $allocationsController . '@show']);


        //=Contents
        $contentsController = 'WA\Http\Controllers\ContentsController';
        $api->get('contents', ['as' => 'api.contents.index', 'uses' => $contentsController . '@index']);
        $api->get('contents/{id}', ['as' => 'api.contents.show', 'uses' => $contentsController. '@show']);
        $api->post('contents', ['uses'=> $contentsController . '@create']);
        $api->put('contents/{id}', ['uses' => $contentsController . '@store']);
        $api->delete('contents/{id}', ['uses' => $contentsController. '@deleteContent']);

        //=App
        $appController = 'WA\Http\Controllers\AppController';
        $api->get('apps', ['as' => 'api.app.index', 'uses' => $appController . '@index']);
        $api->get('apps/{id}', ['as' => 'api.app.show', 'uses' => $appController . '@show']);
        $api->post('apps', ['uses' => $appController . '@create']);
        $api->put('apps/{id}', ['uses' => $appController . '@store']);
        $api->delete('apps/{id}', ['uses' => $appController . '@delete']);

        //=Order
        $orderController = 'WA\Http\Controllers\OrderController';
        $api->get('orders', ['as' => 'api.order.index', 'uses' => $orderController . '@index']);
        $api->get('orders/{id}', ['as' => 'api.order.show', 'uses' => $orderController . '@show']);
        $api->post('orders', ['uses' => $orderController . '@create']);
        $api->put('orders/{id}', ['uses' => $orderController . '@store']);
        $api->delete('orders/{id}', ['uses' => $orderController . '@delete']);

        //=Package
        $packageController = 'WA\Http\Controllers\PackageController';
        $api->get('packages', ['as' => 'api.package.index', 'uses' => $packageController . '@index']);
        $api->get('packages/{id}', ['as' => 'api.package.show', 'uses' => $packageController . '@show']);
        $api->post('packages', ['uses' => $packageController . '@create']);
        $api->put('packages/{id}', ['uses' => $packageController . '@store']);
        $api->delete('packages/{id}', ['uses' => $packageController . '@delete']);

        //=Request
        $requestController = 'WA\Http\Controllers\RequestController';
        $api->get('requests', ['as' => 'api.request.index', 'uses' => $requestController . '@index']);
        $api->get('requests/{id}', ['as' => 'api.request.show', 'uses' => $requestController . '@show']);
        $api->post('requests', ['uses' => $requestController . '@create']);
        $api->put('requests/{id}', ['uses' => $requestController . '@store']);
        $api->delete('requests/{id}', ['uses' => $requestController . '@delete']);

        //=Service
        $serviceController = 'WA\Http\Controllers\ServiceController';
        $api->get('services', ['as' => 'api.service.index', 'uses' => $serviceController . '@index']);
        $api->get('services/{id}', ['as' => 'api.service.show', 'uses' => $serviceController . '@show']);
        $api->post('services', ['uses' => $serviceController . '@create']);
        $api->put('services/{id}', ['uses' => $serviceController . '@store']);
        $api->delete('services/{id}', ['uses' => $serviceController . '@delete']);

    });
});
