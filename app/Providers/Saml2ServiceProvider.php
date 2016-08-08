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

class Saml2ServiceProvider extends Saml2SP
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
    	$this->app->singleton('Aacotroneo\Saml2\Saml2Auth', function ($app) {
    		$config = config('saml2_settings');
            //Log::info("CONFIG: ".print_r($config, true));
        	
            // IF Route == dosso we get the idCompany from Session.
            // IF Route != dosso we get the idCompany from Request.
            //Log::info("ROUTE: ".print_r(Route::getFacadeRoot()->current(), true));
            if(Route::getFacadeRoot()->currentRouteNamed('dosso')){
                //Log::info("RUTA DOSSO");
	       		$idCompany = Session::get('saml2_idcompany');
	       	} else {
                //Log::info("RUTA ELSE -> REQUEST: ".print_r(app('request'), true));
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
            //Log::info("METADATA URL : ".print_r($config['sp']['entityId'], true));
    		$config['sp']['assertionConsumerService']['url'] = 
                URL::route('saml_acs').'?idCompany='.$idCompany;
            //Log::info("ACS URL : ".print_r($config['sp']['assertionConsumerService'], true));
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