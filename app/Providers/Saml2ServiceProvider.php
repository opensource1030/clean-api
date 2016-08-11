<?php

/**
 * Saml2ServiceProvider - Extends Saml2SP and modifies Saml2_settings on the fly.
 *  
 * @author   Agustí Dosaiguas
 */

namespace WA\Providers;

use Aacotroneo\Saml2\Saml2ServiceProvider as Saml2SP;
use Log;
use Cache;
use URL;
use OneLogin_Saml2_Auth;
use Illuminate\Support\ServiceProvider;
use Session;
use Illuminate\Support\Facades\Route;
use Request;

class Saml2ServiceProvider extends Saml2SP
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        if(config('saml2_settings.useRoutes', false) == true ){

            $this->app->group (
                [
                    'prefix' => config('saml2_settings.routesPrefix'),
                    'middleware' => config('saml2_settings.routesMiddleware'),
                ], 
                function ($app)
                {
                    $app->get('logout', ['as' => 'saml_logout', function () use ($app) {
                        $controller = $app->make('Aacotroneo\Saml2\Http\Controllers\Saml2Controller');
                        return $controller->logout();
                    }]);

                    $app->get('login', ['as' => 'saml_login', function () use ($app) {
                        $controller = $app->make('Aacotroneo\Saml2\Http\Controllers\Saml2Controller');
                        return $controller->login();
                    }]);

                    $app->get('metadata', ['as' => 'saml_metadata', function () use ($app) {
                        $controller = $app->make('Aacotroneo\Saml2\Http\Controllers\Saml2Controller');
                        return $controller->metadata();
                    }]);

                    $app->post('acs', ['as' => 'saml_acs', function () use ($app) {
                        $controller = $app->make('WA\Http\Controllers\Auth\Saml2Controller');
                        return $controller->acs();
                    }]);

                    $app->get('sls', ['as' => 'saml_sls', function () use ($app) {
                        $controller = $app->make('Aacotroneo\Saml2\Http\Controllers\Saml2Controller');
                        return $controller->sls();
                    }]);
                }
            );
            //include __DIR__ . '/../../routes.php';
        }

        $this->publishes([
            __DIR__.'/../../config/saml2_settings.php' => config_path('saml2_settings.php'),
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
    	$this->app->singleton('Aacotroneo\Saml2\Saml2Auth', function ($app) {
    		$config = config('saml2_settings');

            $method = Request::getMethod();
            $pathInfo = Request::getPathInfo();
            $route = $app->getRoutes();

            if(stripos($pathInfo, 'doSSO') > 0){
                $email = str_replace("/doSSO/", "", $pathInfo);
	       		$idCompany = Cache::get('saml2_idcompany_'.$email);
	       	} else {
	       		$idCompany = app('request')->get('idCompany'); 
	       	}

            //Log::info("IDCOMPANY: ".print_r($idCompany, true));
            if (!isset($idCompany)) {
                abort(404);
            }

            // Load Saml2Settings from IdCompany.
            $company = app()->make('WA\Repositories\Company\CompanyInterface');
            $companyByID = $company->byId($idCompany);
            if(!isset($companyByID)){
                abort(404);
            }

            $saml2Settings = $companyByID->saml2Settings();

            // Route information
    		$config['sp']['entityId'] = 
                URL::route('saml_metadata').'?idCompany='.$idCompany;

    		$config['sp']['assertionConsumerService']['url'] = 
                URL::route('saml_acs').'?idCompany='.$idCompany;

    		$config['sp']['singleLogoutService']['url'] = 
                URL::route('saml_sls').'?idCompany='.$idCompany;
            //Log::info("SLS : ".print_r($config['sp']['singleLogoutService'], true));                

            // Saml2_Settings Information.
    		$config['idp']['entityId'] = 
    		      $saml2Settings['attributes']['entityId'];

    		$config['idp']['singleSignOnService']['url'] = 
    		      $saml2Settings['attributes']['singleSignOnServiceUrl'];

    		$config['idp']['singleSignOnService']['binding'] = 
    		      $saml2Settings['attributes']['singleSignOnServiceBinding'];

    		$config['idp']['singleLogoutService']['url'] = 
    		      $saml2Settings['attributes']['singleLogoutServiceUrl'];

    		$config['idp']['singleLogoutService']['binding'] = 
    		      $saml2Settings['attributes']['singleLogoutServiceBinding'];

    		$config['idp']['x509cert'] = 
    		      $saml2Settings['attributes']['x509cert'];

    		$auth = new OneLogin_Saml2_Auth($config);
    		return new \Aacotroneo\Saml2\Saml2Auth($auth);
    	});
    }
}