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

 /////////////////////////////////////
$api->version('v1', function ($api) {
    $api->get('redirect', function () { 
        $query = http_build_query([ 
            'client_id' => '3', 
            'redirect_uri' => 'clean.api/callback',
            'response_type' => 'code', 
            'scope' => '' 
        ]);
        return redirect('http://clean.api/oauth/authorize?'.$query); 
    });

    ///////////Routes//////
    $apiA = '\Dusterio\LumenPassport\Http\Controllers\AccessTokenController';
    $api->post('oauth/token', ['as' => 'api.token', 'uses' => $apiA.'@issueToken']);
 
    $apiAATC = '\Laravel\Passport\Http\Controllers\AuthorizedAccessTokenController';
    $api->get('/oauth/tokens', ['as' => 'api.tokens', 'uses' => $apiAATC.'@forUser']);
    $api->delete('/oauth/tokens/{token_id}', ['as' => 'api.tokens.id', 'uses' => $apiAATC.'@destroy']); 
    $apiTTC = '\Laravel\Passport\Http\Controllers\TransientTokenController';
    $api->post('/oauth/token/refresh', ['as' => 'api.refresh', 'uses' => $apiTTC.'@refresh']);
 
    $apiCC = '\Laravel\Passport\Http\Controllers\ClientController';
    $api->get('/oauth/clients', ['as' => 'api.foruser', 'uses' => $apiCC.'@forUser']);
    $api->post('/oauth/clients', ['as' => 'api.store', 'uses' => $apiCC.'@store']);
    $api->put('/oauth/clients/{client_id}', ['as' => 'api.update', 'uses' => $apiCC.'@update']);
    $api->delete('/oauth/clients/{client_id}', ['as' => 'api.destroy', 'uses' => $apiCC.'@destroy']);
 
    $apiSC = '\Laravel\Passport\Http\Controllers\ScopeController';
    $api->get('/oauth/scopes', ['as' => 'api.all', 'uses' => $apiSC.'@all']);
 
    $apiATC = '\WA\Auth\PersonalAccessTokenController';
    $api->get('/oauth/personal-access-tokens', ['as' => 'api.foruser', 'uses' => $apiATC.'@forUser']);
    $api->post('/oauth/personal-access-tokens', ['as' => 'api.store', 'uses' => $apiATC.'@store']);
    $api->delete('/oauth/personal-access-tokens/{token_id}', ['as' => 'api.destroy', 'uses' => $apiATC.'@destroy']);
    //$api->post('/create', [ 'middleware'=>['api.auth','scope:create'],'as' => 'api.create',  'uses' => $apiATC.'@create']);

    $api->get('callback', 
        function (Illuminate\Http\Request $request) { 
           $http = new \GuzzleHttp\Client; 
           $response = $http->post('http://clean.api/oauth/token', 
            [
              'form_params' => 
              [ 
                'client_id' => '3',
                'client_secret' => 'gh9PDSgi1iW963kl21TZ3RlOP1rhcCOSSm6USTSt',
                'grant_type' => 'authorization_code',
                'redirect_uri' => 'clean.api/dashboard', 
                'code' => $request->code, 
              ], 
        ]); 
    return json_decode((string) $response->getBody(), true); 
    }
    );

    $api->get('/user/{id}', function ($id) { 
    return \WA\DataStore\User\User::find($id)->email; 
    });

    // PUBLIC - GET IMAGE
    $api->get('images/{id}', [
        //'middleware' => [$scopeMiddleware.':get_image'],
        'as' => 'api.image.show',
        'uses' => 'WA\Http\Controllers\ImagesController@show'
    ]);

    $api->get('/', function () {
        return response()->json([
            'app_name'    => env('API_NAME', 'clean'),
            'app_version'    => env('APP_VERSION', 'NaN'),
            'app_support'    => env('SUPPORT_EMAIL', 'devsuuport@wirelessanalytics.com'),
            'api_version' => env('API_VERSION', 'v1'),
            'api_domain'  => env('API_DOMAIN', 'api.wirelessanalytics.com')
        ]);
    });

    $ssoAuth = 'WA\Http\Controllers\Auth\SSO';
    $api->get('doSSO/{email}', [
        'as'   => 'dosso_login',
        'uses' => $ssoAuth . '@loginRequest'
    ]);

    $api->get('doSSO/login/{uuid}', [
        'as'   => 'dosso',
        'uses' => $ssoAuth . '@loginUser'
    ]);

    $auth = 'WA\Auth\Auth';
    $api->get('resetPassword/{email}', ['as' => 'reset.email.credentials', 'uses' => $auth . '@resetPassword']);
    $api->get('resetPassword/{identification}/{code}', ['as' => 'reset.password.credentials', 'uses' => $auth . '@getPasswordFromEmail']);
    $api->get('acceptUser/{identification}/{code}', ['as' => 'activate.email.credentials', 'uses' => $auth . '@acceptUser']);

    $api->post('users', [ 'uses' => 'WA\Http\Controllers\UsersController@create' ]);

    // Only for debugging purposes:
    $api->get('deskpro/debug', [
        'middleware' => [],
        'uses' => 'WA\Http\Controllers\CompaniesController@debugEndpoint'
    ]);

    $middleware = [];
    if (!app()->runningUnitTests()) {
        if (env('API_AUTH_MIDDLEWARE') !== null) {
            $middleware[] = env('API_AUTH_MIDDLEWARE', 'auth:api');
        }
    }

    $api->group(['middleware' => $middleware], function ($api) {

        $apiATC = '\Laravel\Passport\Http\Controllers\PersonalAccessTokenController';

        $scopeMiddleware = 'scope';
        if (app()->runningUnitTests()) {
            $scopeMiddleware = 'scopeTest';
        }

        $api->post('/create', [
            'middleware' => [$scopeMiddleware.':create'],
            'as' => 'api.create',
            'uses' => $apiATC.'@create'
        ]);

        // ADDRESSES
        $addressController = 'WA\Http\Controllers\AddressController';

        $api->get('addresses', [
            'middleware' => [$scopeMiddleware.':get_addresses'],
            'as' => 'api.addresses.index',
            'uses' => $addressController . '@index'
        ]);

        $api->get('addresses/{id}', [
            'middleware' => [$scopeMiddleware.':get_address'],
            'as' => 'api.addresses.show',
            'uses' => $addressController . '@show'
        ]);

        $api->post('addresses', [
            'middleware' => [$scopeMiddleware.':create_address'],
            'uses' => $addressController . '@create'
        ]);

        $api->patch('addresses/{id}', [
            'middleware' => [$scopeMiddleware.':update_address'],
            'uses' => $addressController . '@store'
        ]);

        $api->delete('addresses/{id}', [
            'middleware' => [$scopeMiddleware.':delete_address'],
            'uses' => $addressController . '@delete'
        ]);

        // ALLOCATIONS
        $allocationsController = 'WA\Http\Controllers\AllocationsController';

        $api->get('allocations', [
            'middleware' => [$scopeMiddleware.':get_allocations'],
            'as' => 'api.allocations.index',
            'uses' => $allocationsController . '@index'
        ]);

        $api->get('allocations/{id}', [
            'middleware' => [$scopeMiddleware.':get_allocation'],
            'as' => 'api.allocations.show',
            'uses' => $allocationsController . '@show'
        ]);

        // APPS
        $appController = 'WA\Http\Controllers\AppsController';

        $api->get('apps', [
            'middleware' => [$scopeMiddleware.':get_apps'],
            'as' => 'api.app.index',
            'uses' => $appController . '@index'
        ]);

        $api->get('apps/{id}', [
            'middleware' => [$scopeMiddleware.':get_app'],
            'as' => 'api.app.show',
            'uses' => $appController . '@show'
        ]);

        $api->post('apps', [
            'middleware' => [$scopeMiddleware.':create_app'],
            'uses' => $appController . '@create'
        ]);

        $api->patch('apps/{id}', [
            'middleware' => [$scopeMiddleware.':update_app'],
            'uses' => $appController . '@store'
        ]);

        $api->delete('apps/{id}', [
            'middleware' => [$scopeMiddleware.':delete_app'],
            'uses' => $appController . '@delete'
        ]);

        // ASSETS
        $assetsController = 'WA\Http\Controllers\AssetsController';

        $api->get('assets', [
            'middleware' => [$scopeMiddleware.':get_assets'],
            'as' => 'assets.index',
            'uses' => $assetsController . '@index'
        ]);

        $api->get('assets/{id}', [
            'middleware' => [$scopeMiddleware.':get_asset'],
            'as' => 'assets.index',
            'uses' => $assetsController . '@show'
        ]);

        // CARRIERS
        $carrierController = 'WA\Http\Controllers\CarriersController';

        $api->get('carriers', [
            'middleware' => [$scopeMiddleware.':get_carriers'],
            'as' => 'api.carrier.index',
            'uses' => $carrierController . '@index'
        ]);

        $api->get('carriers/{id}', [
            'middleware' => [$scopeMiddleware.':get_carrier'],
            'as' => 'api.carrier.show',
            'uses' => $carrierController . '@show'
        ]);

        $api->post('carriers', [
            'middleware' => [$scopeMiddleware.':create_carrier'],
            'uses' => $carrierController . '@create'
        ]);

        $api->patch('carriers/{id}', [
            'middleware' => [$scopeMiddleware.':update_carrier'],
            'uses' => $carrierController . '@store'
        ]);

        $api->delete('carriers/{id}', [
            'middleware' => [$scopeMiddleware.':delete_carrier'],
            'uses' => $carrierController . '@delete'
        ]);

        // COMPANIES
        $companiesController = 'WA\Http\Controllers\CompaniesController';

        $api->get('companies', [
            'middleware' => [$scopeMiddleware.':get_companies'],
            'as' => 'api.company.index',
            'uses' => $companiesController . '@index'
        ]);

        $api->get('companies/{id}', [
            'middleware' => [$scopeMiddleware.':get_company'],
            'as' => 'api.company.show',
            'uses' => $companiesController . '@show'
        ]);

        $api->post('companies', [
            'middleware' => [$scopeMiddleware.':create_company'],
            'uses' => $companiesController . '@create'
        ]);

        $api->patch('companies/{id}', [
            'middleware' => [$scopeMiddleware.':update_company'],
            'uses' => $companiesController . '@store'
        ]);

        $api->delete('companies/{id}', [
            'middleware' => [$scopeMiddleware.':delete_company'],
            'uses' => $companiesController . '@deleteCompany'
        ]);

        // ==Company Jobs
        $api->get('companies/{companyId}/jobs', [
            'middleware' => [$scopeMiddleware.':get_company_jobs'],
            'uses' => $companiesController . '@showJobs'
        ]);
        
        $api->get('companies/{companyId}/jobs/{jobId}', [
            'middleware' => [$scopeMiddleware.':get_company_job'],
            'uses' => $companiesController . '@showJob'
        ]);

        $api->post('companies/{companyId}/jobs', [
            'middleware' => [$scopeMiddleware.':create_company_job'],
            'uses' => $companiesController . '@createJob'
        ]);

        $api->patch('companies/{companyId}/jobs/{jobId}', [
            'middleware' => [$scopeMiddleware.':update_company_job'],
            'uses' => $companiesController . '@storeJob'
        ]);

        $api->delete('companies/{companyId}/jobs/{jobId}', [
            'middleware' => [$scopeMiddleware.':delete_company_job'],
            'uses' => $companiesController . '@deleteJob'
        ]);


        // CONDITIONS
        $conditionsController = 'WA\Http\Controllers\ConditionsController';

        $api->get('conditions', [
            'middleware' => [$scopeMiddleware.':get_conditions'],
            'as' => 'api.conditions.index',
            'uses' => $conditionsController . '@index'
        ]);

        $api->get('conditions/{id}', [
            'middleware' => [$scopeMiddleware.':get_condition'],
            'as' => 'api.conditions.show',
            'uses' => $conditionsController . '@show'
        ]);

        $api->post('conditions', [
            'middleware' => [$scopeMiddleware.':create_condition'],
            'uses' => $conditionsController . '@create'
        ]);

        $api->patch('conditions/{id}', [
            'middleware' => [$scopeMiddleware.':updatecondition'],
            'uses' => $conditionsController . '@store'
        ]);

        $api->delete('conditions/{id}', [
            'middleware' => [$scopeMiddleware.':delete_condition'],
            'uses' => $conditionsController . '@delete'
        ]);

        // CONTENTS
        $contentsController = 'WA\Http\Controllers\ContentsController';

        $api->get('contents', [
            'middleware' => [$scopeMiddleware.':get_contents'],
            'as' => 'api.contents.index',
            'uses' => $contentsController . '@index'
        ]);

        $api->get('contents/{id}', [
            'middleware' => [$scopeMiddleware.':get_content'],
            'as' => 'api.contents.show',
            'uses' => $contentsController . '@show'
        ]);

        $api->post('contents', [
            'middleware' => [$scopeMiddleware.':create_content'],
            'uses' => $contentsController . '@create'
        ]);

        $api->patch('contents/{id}', [
            'middleware' => [$scopeMiddleware.':update_content'],
            'uses' => $contentsController . '@store'
        ]);

        $api->delete('contents/{id}', [
            'middleware' => [$scopeMiddleware.':delete_content'],
            'uses' => $contentsController . '@deleteContent'
        ]);

        // DEVICES
        $devicesController = 'WA\Http\Controllers\DevicesController';

        $api->get('devices', [
            'middleware' => [$scopeMiddleware.':get_devices'],
            'as' => 'api.devices.index',
            'uses' => $devicesController . '@index'
        ]);

        $api->get('devices/{id}', [
            'middleware' => [$scopeMiddleware.':get_device'],
            'as' => 'api.devices.show',
            'uses' => $devicesController . '@show'
        ]);

        $api->post('devices', [
            'middleware' => [$scopeMiddleware.':create_device'],
            'uses' => $devicesController . '@create'
        ]);

        $api->patch('devices/{id}', [
            'middleware' => [$scopeMiddleware.':update_device'],
            'uses' => $devicesController . '@store'
        ]);

        $api->delete('devices/{id}', [
            'middleware' => [$scopeMiddleware.':delete_device'],
            'uses' => $devicesController . '@delete'
        ]);

        // DEVICETYPES
        $devicesTypeController = 'WA\Http\Controllers\DeviceTypesController';

        $api->get('devicetypes', [
            'middleware' => [$scopeMiddleware.':get_devicetypes'],
            'as' => 'api.devicetype.index',
            'uses' => $devicesTypeController . '@index'
        ]);

        $api->get('devicetypes/{id}', [
            'middleware' => [$scopeMiddleware.':get_devicetype'],
            'as' => 'api.devicetype.show',
            'uses' => $devicesTypeController . '@show'
        ]);

        $api->post('devicetypes', [
            'middleware' => [$scopeMiddleware.':create_devicetype'],
            'uses' => $devicesTypeController . '@create'
        ]);

        $api->patch('devicetypes/{id}', [
            'middleware' => [$scopeMiddleware.':update_devicetype'],
            'uses' => $devicesTypeController . '@store'
        ]);

        $api->delete('devicetypes/{id}', [
            'middleware' => [$scopeMiddleware.':delete_devicetype'],
            'uses' => $devicesTypeController . '@delete'
        ]);

        // DEVICEVARIATIONS
        $deviceVariationsController = 'WA\Http\Controllers\DeviceVariationsController';

        $api->get('devicevariations', [
            'middleware' => [$scopeMiddleware.':get_devicevariations'],
            'as' => 'api.deviceVariation.index',
            'uses' => $deviceVariationsController . '@index'
        ]);

        $api->get('devicevariations/{id}', [
            'middleware' => [$scopeMiddleware.':get_devicevariation'],
            'as' => 'api.deviceVariation.show',
            'uses' => $deviceVariationsController . '@show'
        ]);

        $api->post('devicevariations', [
            'middleware' => [$scopeMiddleware.':create_devicevariation'],
            'uses' => $deviceVariationsController . '@create'
        ]);

        $api->patch('devicevariations/{id}', [
            'middleware' => [$scopeMiddleware.':update_devicevariation'],
            'uses' => $deviceVariationsController . '@store'
        ]);

        $api->delete('devicevariations/{id}', [
            'middleware' => [$scopeMiddleware.':delete_devicevariation'],
            'uses' => $deviceVariationsController . '@delete'
        ]);

        // IMAGES
        $imageController = 'WA\Http\Controllers\ImagesController';

        $api->get('images', [
            'middleware' => [$scopeMiddleware.':get_images'],
            'as' => 'api.image.index',
            'uses' => $imageController . '@index'
        ]);

        $api->get('images/info/{id}', [
            'middleware' => [$scopeMiddleware.':get_image_info'],
            'as' => 'api.image.info',
            'uses' => $imageController . '@info'
        ]);

        $api->post('images', [
            'middleware' => [$scopeMiddleware.':create_image'],
            'uses' => $imageController . '@create'
        ]);

        $api->delete('images/{id}', [
            'middleware' => [$scopeMiddleware.':delete_image'],
            'uses' => $imageController . '@delete'
        ]);

        // LOCATIONS
        $locationController = 'WA\Http\Controllers\LocationsController';

        $api->get('locations', [
            'middleware' => [$scopeMiddleware.':get_locations'],
            'as' => 'api.location.index',
            'uses' => $locationController . '@index'
        ]);

        // MODIFICATIONS
        $modificationController = 'WA\Http\Controllers\ModificationsController';

        $api->get('modifications', [
            'middleware' => [$scopeMiddleware.':get_modifications'],
            'as' => 'api.modification.index',
            'uses' => $modificationController . '@index'
        ]);

        $api->get('modifications/{id}', [
            'middleware' => [$scopeMiddleware.':get_modification'],
            'as' => 'api.modification.show',
            'uses' => $modificationController . '@show'
        ]);

        $api->post('modifications', [
            'middleware' => [$scopeMiddleware.':create_modification'],
            'uses' => $modificationController . '@create'
        ]);

        $api->patch('modifications/{id}', [
            'middleware' => [$scopeMiddleware.':update_modification'],
            'uses' => $modificationController . '@store'
        ]);

        $api->delete('modifications/{id}', [
            'middleware' => [$scopeMiddleware.':delete_modification'],
            'uses' => $modificationController . '@delete'
        ]);

        // ORDERS
        $orderController = 'WA\Http\Controllers\OrdersController';

        $api->get('orders', [
            'middleware' => [$scopeMiddleware.':get_orders'],
            'as' => 'api.order.index',
            'uses' => $orderController . '@index'
        ]);

        $api->get('orders/{id}', [
            'middleware' => [$scopeMiddleware.':get_order'],
            'as' => 'api.order.show',
            'uses' => $orderController . '@show'
        ]);

        $api->post('orders', [
            'middleware' => [$scopeMiddleware.':create_order'],
            'uses' => $orderController . '@create'
        ]);

        $api->patch('orders/{id}', [
            'middleware' => [$scopeMiddleware.':update_order'],
            'uses' => $orderController . '@store'
        ]);

        $api->delete('orders/{id}', [
            'middleware' => [$scopeMiddleware.':delete_order'],
            'uses' => $orderController . '@delete'
        ]);

        // PACKAGES
        $packageController = 'WA\Http\Controllers\PackagesController';

        $api->get('packages', [
            'middleware' => [$scopeMiddleware.':get_packages'],
            'as' => 'api.package.index',
            'uses' => $packageController . '@index'
        ]);

        $api->post('packages/forUser', [
            'middleware' => [$scopeMiddleware.':get_packages_foruser'],
            'as' => 'api.package.userpackages',
            'uses' => $packageController . '@userPackages'
        ]);

        $api->get('packages/{id}', [
            'middleware' => [$scopeMiddleware.':get_package'],
            'as' => 'api.package.show',
            'uses' => $packageController . '@show'
        ]);

        $api->post('packages', [
            'middleware' => [$scopeMiddleware.':create_package'],
            'uses' => $packageController . '@create'
        ]);

        $api->patch('packages/{id}', [
            'middleware' => [$scopeMiddleware.':update_package'],
            'uses' => $packageController . '@store'
        ]);

        $api->delete('packages/{id}', [
            'middleware' => [$scopeMiddleware.':delete_package'],
            'uses' => $packageController . '@delete'
        ]);

        // PRESETS
        $presetController = 'WA\Http\Controllers\PresetsController';

        $api->get('presets', [
            'middleware' => [$scopeMiddleware.':get_presets'],
            'as' => 'api.presets.index',
            'uses' => $presetController . '@index'
        ]);

        $api->get('presets/{id}', [
            'middleware' => [$scopeMiddleware.':get_preset'],
            'as' => 'api.presets.show',
            'uses' => $presetController . '@show'
        ]);

        $api->post('presets', [
            'middleware' => [$scopeMiddleware.':create_preset'],
            'uses' => $presetController . '@create'
        ]);

        $api->patch('presets/{id}', [
            'middleware' => [$scopeMiddleware.':update_preset'],
            'uses' => $presetController . '@store'
        ]);

        $api->delete('presets/{id}', [
            'middleware' => [$scopeMiddleware.':delete_preset'],
            'uses' => $presetController . '@delete'
        ]);

        // REQUESTS
        $requestController = 'WA\Http\Controllers\RequestsController';

        $api->get('requests', [
            'middleware' => [$scopeMiddleware.':get_requests'],
            'as' => 'api.request.index',
            'uses' => $requestController . '@index'
        ]);

        $api->get('requests/{id}', [
            'middleware' => [$scopeMiddleware.':get_request'],
            'as' => 'api.request.show',
            'uses' => $requestController . '@show'
        ]);

        $api->post('requests', [
            'middleware' => [$scopeMiddleware.':create_request'],
            'uses' => $requestController . '@create'
        ]);

        $api->patch('requests/{id}', [
            'middleware' => [$scopeMiddleware.':update_request'],
            'uses' => $requestController . '@store'
        ]);

        $api->delete('requests/{id}', [
            'middleware' => [$scopeMiddleware.':delete_request'],
            'uses' => $requestController . '@delete'
        ]);

        // SERVICES
        $serviceController = 'WA\Http\Controllers\ServicesController';

        $api->get('services', [
            'middleware' => [$scopeMiddleware.':get_services'],
            'as' => 'api.service.index',
            'uses' => $serviceController . '@index'
        ]);

        $api->get('services/{id}', [
            'middleware' => [$scopeMiddleware.':get_service'],
            'as' => 'api.service.show',
            'uses' => $serviceController . '@show'
        ]);

        $api->post('services', [
            'middleware' => [$scopeMiddleware.':create_service'],
            'uses' => $serviceController . '@create'
        ]);

        $api->patch('services/{id}', [
            'middleware' => [$scopeMiddleware.':update_service'],
            'uses' => $serviceController . '@store'
        ]);

        $api->delete('services/{id}', [
            'middleware' => [$scopeMiddleware.':delete_service'],
            'uses' => $serviceController . '@delete'
        ]);

        // USERS
        $usersController = 'WA\Http\Controllers\UsersController';

        $api->get('users', [
            'middleware' => [$scopeMiddleware.':get_users'],
            'as' => 'api.users.index',
            'uses' => $usersController . '@index'
        ]);

        $api->get('users/packages/{userId}', [
            'middleware' => [$scopeMiddleware.':get_users_packages'],
            'as' => 'api.users.number',
            'uses' => $usersController . '@usersPackages'
        ]);

        $api->get('users/me', [
            'middleware' => [$scopeMiddleware.':get_user_me'],
            'as' => 'api.users.logged',
            'uses' => $usersController . '@getLoggedInUser'
        ]);

        $api->get('users/jwt', [
            //'middleware' => [$scopeMiddleware.':jwt_authenticate'],
            'uses' => $usersController . '@getJwtUserToken'
        ]);

        $api->get('users/{id}', [
            'middleware' => [$scopeMiddleware.':get_user'],
            'as' => 'api.users.show',
            'uses' => $usersController . '@show'
        ]);

        $api->patch('users/{id}', [
            'middleware' => [$scopeMiddleware.':update_user'],
            'uses' => $usersController . '@store'
        ]);

        $api->delete('users/{id}', [
            'middleware' => [$scopeMiddleware.':delete_user'],
            'uses' => $usersController . '@delete'
        ]);


        // CATEGORYAPPS
        $categoryAppController = 'WA\Http\Controllers\CategoryAppsController';

        $api->get('categoryapps', [
            'middleware' => [$scopeMiddleware.':get_categoryapps'],
            'as' => 'api.categoryapps.index',
            'uses' => $categoryAppController . '@index'
        ]);

        $api->get('categoryapps/{id}', [
            'middleware' => [$scopeMiddleware.':get_categoryapp'],
            'as' => 'api.categoryapps.show',
            'uses' => $categoryAppController . '@show'
        ]);

        $api->post('categoryapps', [
            'middleware' => [$scopeMiddleware.':create_categoryapp'],
            'uses' => $categoryAppController . '@create'
        ]);

        $api->patch('categoryapps/{id}', [
            'middleware' => [$scopeMiddleware.':update_categoryapp'],
            'uses' => $categoryAppController . '@store'
        ]);

        $api->delete('categoryapps/{id}', [
            'middleware' => [$scopeMiddleware.':delete_categoryapp'],
            'uses' => $categoryAppController . '@delete'
        ]);
/*
        // CONDITIONFIELDS
        $conditionFieldsController = 'WA\Http\Controllers\ConditionFieldsController';

        $api->get('conditionsfields', [
            'middleware' => [$scopeMiddleware.':get_conditionsfields'],
            'as' => 'api.conditionfields.index',
            'uses' => $conditionFieldsController . '@index'
        ]);

        $api->get('conditionsfields/{id}', [
            'middleware' => [$scopeMiddleware.':get_conditionsfield'],
            'as' => 'api.conditionfields.show',
            'uses' => $conditionFieldsController . '@show'
        ]);

        $api->post('conditionsfields', [
            'middleware' => [$scopeMiddleware.':create_conditionsfield'],
            'uses' => $conditionFieldsController . '@create'
        ]);

        $api->patch('conditionsfields/{id}', [
            'middleware' => [$scopeMiddleware.':update_conditionsfield'],
            'uses' => $conditionFieldsController . '@store'
        ]);

        $api->delete('conditionsfields/{id}', [
            'middleware' => [$scopeMiddleware.':delete_conditionsfield'],
            'uses' => $conditionFieldsController . '@delete'
        ]);

        // CONDITIONS OPERATORS
        $conditionOpController = 'WA\Http\Controllers\ConditionOperatorsController';
        
        $api->get('conditionsoperators', [
            'middleware' => [$scopeMiddleware.':get_conditionsoperators'],
            'as' => 'api.conditionoperators.index',
            'uses' => $conditionOpController . '@index'
        ]);

        $api->get('conditionsoperators/{id}', [
            'middleware' => [$scopeMiddleware.':get_conditionsoperator'],
            'as' => 'api.conditionoperators.show',
            'uses' => $conditionOpController.'@show'
        ]);

        $api->post('conditionsoperators', [
            'middleware' => [$scopeMiddleware.':create_conditionsoperator'],
            'uses' => $conditionOpController . '@create'
        ]);

        $api->patch('conditionsoperators/{id}', [
            'middleware' => [$scopeMiddleware.':update_conditionsoperator'],
            'uses' => $conditionOpController . '@store'
        ]);

        $api->delete('conditionsoperators/{id}', [
            'middleware' => [$scopeMiddleware.':delete_conditionsoperator'],
            'uses' => $conditionOpController . '@delete'
        ]);
*/
        // ROLES
        $rolesController = 'WA\Http\Controllers\RolesController';

        $api->get('roles', [
            'middleware' => [$scopeMiddleware.':get_roles'],
            'as' => 'api.roles.index',
            'uses' => $rolesController . '@index'
        ]);

        $api->get('roles/{id}', [
            'middleware' => [$scopeMiddleware.':get_role'],
            'as' => 'api.roles.show',
            'uses' => $rolesController . '@show'
        ]);

        $api->post('roles', [
            'middleware' => [$scopeMiddleware.':create_role'],
            'uses' => $rolesController . '@create'
        ]);

        $api->put('roles/{id}', [
            'middleware' => [$scopeMiddleware.':update_role'],
            'uses' => $rolesController . '@store'
        ]);

        $api->delete('roles/{id}', [
            'middleware' => [$scopeMiddleware.':delete_role'],
            'uses' => $rolesController . '@delete'
        ]);

        // PERMISSIONS
        $permissionsController = 'WA\Http\Controllers\PermissionsController';

        $api->get('permissions', [
            'middleware' => [$scopeMiddleware.':get_permissions'],
            'as' => 'api.permissions.index',
            'uses' => $permissionsController . '@index'
        ]);

        $api->get('permissions/{id}', [
            'middleware' => [$scopeMiddleware.':get_permission'],
            'as' => 'api.permissions.show',
            'uses' => $permissionsController . '@show'
        ]);

        $api->post('permissions', [
            'middleware' => [$scopeMiddleware.':create_permission'],
            'uses' => $permissionsController . '@create'
        ]);

        $api->put('permissions/{id}', [
            'middleware' => [$scopeMiddleware.':update_permission'],
            'uses' => $permissionsController . '@store'
        ]);

        $api->delete('permissions/{id}', [
            'middleware' => [$scopeMiddleware.':delete_permission'],
            'uses' => $permissionsController . '@delete'
        ]);

        // SCOPES
        $scopesController = 'WA\Http\Controllers\ScopesController';
        $api->get('scopes', [
            'middleware' => [$scopeMiddleware.':get_scopes'],
            'as' => 'api.scopes.index',
            'uses' => $scopesController . '@index'
        ]);

        $api->get('scopes/{id}', [
            'middleware' => [$scopeMiddleware.':get_scope'],
            'as' => 'api.scopes.show',
            'uses' => $scopesController . '@show'
        ]);

        $api->post('scopes', [
            'middleware' => [$scopeMiddleware.':create_scope'],
            'uses' => $scopesController . '@create'
        ]);

        $api->put('scopes/{id}', [
            'middleware' => [$scopeMiddleware.':update_scope'],
            'uses' => $scopesController . '@store'
        ]);

        $api->delete('scopes/{id}', [
            'middleware' => [$scopeMiddleware.':delete_scope'],
            'uses' => $scopesController . '@delete'
        ]);

        // RELATIONSHIPS
        $api->get('{model}/{id}/relationships/{include}', function ($model, $id, $include) {
            $controller = app()->make("\\WA\\Http\\Controllers\\RelationshipsController");
            return $controller->includeRelationships($model, $id, $include);
        });

        $api->get('{model}/{id}/{include}', function ($model, $id, $include) {
            $controller = app()->make("\\WA\\Http\\Controllers\\RelationshipsController");
            return $controller->includeInformationRelationships($model, $id, $include);
        });

        // JOBS
        $jobsController = 'WA\Http\Controllers\JobsController';
        $api->put('jobs/updateBillingMonths', [
            'middleware' => [$scopeMiddleware.':update_jobsbillingmonths'],
            'uses' => $jobsController . '@updateBillingMonths']);

        // Stripes
        $paymentsController = 'WA\Http\Controllers\PaymentsController';
        //$api->get('payments', ['as' => 'api.payments.index', 'uses' => $paymentsController . '@index']);
        //$api->get('payments/{id}', ['as' => 'api.payments.show', 'uses' => $paymentsController . '@show']);
        $api->post('payments', ['uses' => $paymentsController . '@create']);
        //$api->patch('payments/{id}', ['uses' => $paymentsController . '@store']);
        $api->delete('payments/{id}', ['uses' => $paymentsController . '@delete']);


        // =GlobalSetting
        $globalSettingController = 'WA\Http\Controllers\GlobalSettingsController';
        $api->get('globalsettings', [
            'middleware' => [$scopeMiddleware.':get_globalsettings'],
            'as' => 'api.globalsettings.index',
            'uses' => $globalSettingController . '@index'
        ]);

        $api->get('globalsettings/{id}', [
            'middleware' => [$scopeMiddleware.':get_globalsetting'],
            'as' => 'api.globalsettings.show',
            'uses' => $globalSettingController . '@show'
        ]);

        $api->post('globalsettings', [
            'middleware' => [$scopeMiddleware.':create_globalsetting'],
            'uses' => $globalSettingController . '@create'
        ]);

        $api->patch('globalsettings/{id}', [
            'middleware' => [$scopeMiddleware.':update_globalsetting'],
            'uses' => $globalSettingController . '@store'
        ]);

        $api->delete('globalsettings/{id}', [
            'middleware' => [$scopeMiddleware.':delete_globalsetting'],
            'uses' => $globalSettingController . '@delete'
        ]);

        // DESKPRO
        $deskproController = 'WA\Http\Controllers\DeskproController';
        $api->get('deskpro', [
            'middleware' => [$scopeMiddleware.':search_deskpro'],
            'as' => 'api.deskpro.search',
            'uses' => $deskproController . '@search'
        ]);
    });
});
