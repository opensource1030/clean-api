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
    $apiA = '\Laravel\Passport\Http\Controllers\AccessTokenController';
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

    $apiATC = '\Laravel\Passport\Http\Controllers\PersonalAccessTokenController';
    $api->get('/oauth/personal-access-tokens', ['as' => 'api.foruser', 'uses' => $apiATC.'@forUser']);
    $api->post('/oauth/personal-access-tokens', ['as' => 'api.store', 'uses' => $apiATC.'@store']);
    $api->delete('/oauth/personal-access-tokens/{token_id}', ['as' => 'api.destroy', 'uses' => $apiATC.'@destroy']);
    ///////////////////////////

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
//////////////////////////////////////

    $api->get('/user/{id}', function ($id) { 
    return \WA\DataStore\User\User::find($id)->email; 
    });

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

    $middleware = [];
    if (!app()->runningUnitTests()) {
        if (env('API_AUTH_MIDDLEWARE') !== null) {
            $middleware[] = env('API_AUTH_MIDDLEWARE', 'api.auth');
        }
    }

    $api->group(['middleware' => $middleware], function ($api) {
        $api->get('{model}/{id}/relationships/{include}', function ($model, $id, $include) {
            $modelName = title_case($model);
            $modelSingular = str_singular($modelName);
            $controllerName = "\\WA\\Http\\Controllers\\${modelName}Controller";
            if (!class_exists($controllerName)) {
                $error['errors'][$model] = \Illuminate\Support\Facades\Lang::get('messages.NotExistClass',
                    ['class' => $modelSingular]);
                return response()->json($error)->setStatusCode(404);
            }
            $controller = app()->make($controllerName);
            return $controller->includeRelationships($model, $id, $include);
        });

        $api->get('{model}/{id}/{include}', function ($model, $id, $include) {
            $modelName = title_case($model);
            $modelSingular = str_singular($modelName);
            $controllerName = "\\WA\\Http\\Controllers\\${modelName}Controller";
            if (!class_exists($controllerName)) {
                $error['errors'][$model] = \Illuminate\Support\Facades\Lang::get('messages.NotExistClass',
                    ['class' => $modelSingular]);
                return response()->json($error)->setStatusCode(404);
            }
            $controller = app()->make($controllerName);
            return $controller->includeInformationRelationships($model, $id, $include);
        });

        // =Companies
        $companiesController = 'WA\Http\Controllers\CompaniesController';
        $api->get('companies', ['as' => 'api.company.index', 'uses' => $companiesController . '@index']);
        $api->get('companies/{id}', ['as' => 'api.company.show', 'uses' => $companiesController . '@show']);
        $api->post('companies', ['uses' => $companiesController . '@create']);
        $api->put('companies/{id}', ['uses' => $companiesController . '@store']);
        $api->delete('companies/{id}', ['uses' => $companiesController . '@deleteCompany']);

        // =Users
        $usersController = 'WA\Http\Controllers\UsersController';
        $api->get('users', ['as' => 'api.users.index', 'uses' => $usersController . '@index']);
        $api->post('users/usersMatchingConditions', ['as' => 'api.users.number', 'uses' => $usersController . '@numberUsers']);
        $api->get('users/{id}', ['as' => 'api.users.show', 'uses' => $usersController . '@show']);
        $api->post('users', [ 'uses' => $usersController . '@create']);
        $api->put('users/{id}', [ 'uses' => $usersController . '@store']);
        $api->delete('users/{id}', [ 'uses' => $usersController . '@delete']);

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
        $api->get('contents/{id}', ['as' => 'api.contents.show', 'uses' => $contentsController . '@show']);
        $api->post('contents', ['uses' => $contentsController . '@create']);
        $api->put('contents/{id}', ['uses' => $contentsController . '@store']);
        $api->delete('contents/{id}', ['uses' => $contentsController . '@deleteContent']);

        //=App
        $appController = 'WA\Http\Controllers\AppsController';
        $api->get('apps', ['as' => 'api.app.index', 'uses' => $appController . '@index']);
        $api->get('apps/{id}', ['as' => 'api.app.show', 'uses' => $appController . '@show']);
        $api->post('apps', ['uses' => $appController . '@create']);
        $api->put('apps/{id}', ['uses' => $appController . '@store']);
        $api->delete('apps/{id}', ['uses' => $appController . '@delete']);

        //=Order
        $orderController = 'WA\Http\Controllers\OrdersController';
        $api->get('orders', ['as' => 'api.order.index', 'uses' => $orderController . '@index']);
        $api->get('orders/{id}', ['as' => 'api.order.show', 'uses' => $orderController . '@show']);
        $api->post('orders', ['uses' => $orderController . '@create']);
        $api->put('orders/{id}', ['uses' => $orderController . '@store']);
        $api->delete('orders/{id}', ['uses' => $orderController . '@delete']);

        //=Package
        $packageController = 'WA\Http\Controllers\PackagesController';
        $api->get('packages', ['as' => 'api.package.index', 'uses' => $packageController . '@index']);
        $api->get('packages/forUser',
            ['as' => 'api.package.userpackages', 'uses' => $packageController . '@userPackages']);
        $api->get('packages/{id}', ['as' => 'api.package.show', 'uses' => $packageController . '@show']);
        $api->post('packages', ['uses' => $packageController . '@create']);
        $api->put('packages/{id}', ['uses' => $packageController . '@store']);
        $api->delete('packages/{id}', ['uses' => $packageController . '@delete']);

        //=Request
        $requestController = 'WA\Http\Controllers\RequestsController';
        $api->get('requests', ['as' => 'api.request.index', 'uses' => $requestController . '@index']);
        $api->get('requests/{id}', ['as' => 'api.request.show', 'uses' => $requestController . '@show']);
        $api->post('requests', ['uses' => $requestController . '@create']);
        $api->put('requests/{id}', ['uses' => $requestController . '@store']);
        $api->delete('requests/{id}', ['uses' => $requestController . '@delete']);

        //=Service
        $serviceController = 'WA\Http\Controllers\ServicesController';
        $api->get('services', ['as' => 'api.service.index', 'uses' => $serviceController . '@index']);
        $api->get('services/{id}', ['as' => 'api.service.show', 'uses' => $serviceController . '@show']);
        $api->post('services', ['uses' => $serviceController . '@create']);
        $api->put('services/{id}', ['uses' => $serviceController . '@store']);
        $api->delete('services/{id}', ['uses' => $serviceController . '@delete']);

        //=Modification
        $modificationController = 'WA\Http\Controllers\ModificationsController';
        $api->get('modifications', ['as' => 'api.modification.index', 'uses' => $modificationController . '@index']);
        $api->get('modifications/{id}', ['as' => 'api.modification.show', 'uses' => $modificationController . '@show']);
        $api->post('modifications', ['uses' => $modificationController . '@create']);
        $api->put('modifications/{id}', ['uses' => $modificationController . '@store']);
        $api->delete('modifications/{id}', ['uses' => $modificationController . '@delete']);

        //=Carrier
        $carrierController = 'WA\Http\Controllers\CarriersController';
        $api->get('carriers', ['as' => 'api.carrier.index', 'uses' => $carrierController . '@index']);
        $api->get('carriers/{id}', ['as' => 'api.carrier.show', 'uses' => $carrierController . '@show']);
        $api->post('carriers', ['uses' => $carrierController . '@create']);
        $api->put('carriers/{id}', ['uses' => $carrierController . '@store']);
        $api->delete('carriers/{id}', ['uses' => $carrierController . '@delete']);

        //=Price
        $priceController = 'WA\Http\Controllers\PricesController';
        $api->get('prices', ['as' => 'api.price.index', 'uses' => $priceController . '@index']);
        $api->get('prices/{id}', ['as' => 'api.price.show', 'uses' => $priceController . '@show']);
        $api->post('prices', ['uses' => $priceController . '@create']);
        $api->put('prices/{id}', ['uses' => $priceController . '@store']);
        $api->delete('prices/{id}', ['uses' => $priceController . '@delete']);

        //=Image
        $imageController = 'WA\Http\Controllers\ImagesController';
        $api->get('images', ['as' => 'api.image.index', 'uses' => $imageController . '@index']);
        $api->get('images/{id}', ['as' => 'api.image.show', 'uses' => $imageController . '@show']);
        $api->get('images/info/{id}', ['as' => 'api.image.info', 'uses' => $imageController . '@info']);
        $api->post('images', ['uses' => $imageController . '@create']);
        $api->delete('images/{id}', ['uses' => $imageController . '@delete']);

        //=Address
        $addressController = 'WA\Http\Controllers\AddressController';
        $api->get('address', ['as' => 'api.address.index', 'uses' => $addressController . '@index']);
        $api->get('address/{id}', ['as' => 'api.address.show', 'uses' => $addressController . '@show']);
        $api->post('address', ['uses' => $addressController . '@create']);
        $api->put('address/{id}', ['uses' => $addressController . '@store']);
        $api->delete('address/{id}', ['uses' => $addressController . '@delete']);

        //=DeviceType
        $devicesTypeController = 'WA\Http\Controllers\DeviceTypesController';
        $api->get('devicetypes', ['as' => 'api.devicetype.index', 'uses' => $devicesTypeController . '@index']);
        $api->get('devicetypes/{id}', ['as' => 'api.devicetype.show', 'uses' => $devicesTypeController . '@show']);
        $api->post('devicetypes', ['uses' => $devicesTypeController . '@create']);
        $api->put('devicetypes/{id}', ['uses' => $devicesTypeController . '@store']);
        $api->delete('devicetypes/{id}', ['uses' => $devicesTypeController . '@delete']);

        //=CategoryDevices
        $presetController = 'WA\Http\Controllers\PresetsController';
        $api->get('presets', ['as' => 'api.presets.index', 'uses' => $presetController . '@index']);
        $api->get('presets/{id}', ['as' => 'api.presets.show', 'uses' => $presetController . '@show']);
        $api->post('presets', ['uses' => $presetController . '@create']);
        $api->put('presets/{id}', ['uses' => $presetController . '@store']);
        $api->delete('presets/{id}', ['uses' => $presetController . '@delete']);

        //=CategoryApps
        $categoryAppController = 'WA\Http\Controllers\CategoryAppsController';
        $api->get('categoryapps', ['as' => 'api.categoryapps.index', 'uses' => $categoryAppController . '@index']);
        $api->get('categoryapps/{id}', ['as' => 'api.categoryapps.show', 'uses' => $categoryAppController . '@show']);
        $api->post('categoryapps', ['uses' => $categoryAppController . '@create']);
        $api->put('categoryapps/{id}', ['uses' => $categoryAppController . '@store']);
        $api->delete('categoryapps/{id}', ['uses' => $categoryAppController . '@delete']);

        //=Conditions
        $conditionsController = 'WA\Http\Controllers\ConditionsController';
        $api->get('conditions', ['as' => 'api.conditions.index', 'uses' => $conditionsController . '@index']);
        $api->get('conditions/{id}', ['as' => 'api.conditions.show', 'uses' => $conditionsController . '@show']);
        $api->post('conditions', ['uses' => $conditionsController . '@create']);
        $api->put('conditions/{id}', ['uses' => $conditionsController . '@store']);
        $api->delete('conditions/{id}', ['uses' => $conditionsController . '@delete']);

        //=ConditionsFields
        $conditionFieldsController = 'WA\Http\Controllers\ConditionFieldsController';
        $api->get('conditionsfields',
            ['as' => 'api.conditionfields.index', 'uses' => $conditionFieldsController . '@index']);
        //$api->get('conditionsfields/{id}', ['as' => 'api.conditionfields.show', 'uses' => $conditionFieldsController . '@show']);
        //$api->post('conditionsfields', ['uses' => $conditionFieldsController . '@create']);
        //$api->put('conditionsfields/{id}', ['uses' => $conditionFieldsController . '@store']);
        //$api->delete('conditionsfields/{id}', ['uses' => $conditionFieldsController . '@delete']);

        //=ConditionsOperator
        $conditionOpController = 'WA\Http\Controllers\ConditionOperatorsController';
        $api->get('conditionsoperators',
            ['as' => 'api.conditionoperators.index', 'uses' => $conditionOpController . '@index']);
        //$api->get('conditionsoperators/{id}', ['as' => 'api.conditionoperators.show', 'uses' => $conditionOpController.'@show']);
        //$api->post('conditionsoperators', ['uses' => $conditionOpController . '@create']);
        //$api->put('conditionsoperators/{id}', ['uses' => $conditionOpController . '@store']);
        //$api->delete('conditionsoperators/{id}', ['uses' => $conditionOpController . '@delete']);

    });
});
