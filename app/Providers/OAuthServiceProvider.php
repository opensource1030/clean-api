<?php


namespace WA\Providers;

use Illuminate\Support\ServiceProvider;
use Dingo\Api\Auth\Auth;
use Dingo\Api\Auth\Provider\OAuth2;

/**
 * Class AdjustmentServiceProvider.
 */
class OAuthServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->app[Auth::class]->extend('oauth', function ($app) {
            $provider = new OAuth2($app['oauth2-server.authorizer']->getChecker());

            $provider->setUserResolver(function ($id) {
                $employee = $this->app->make('WA\Repositories\Employee\EmployeeInterface');

                return $employee->byId('$id');
            });

            $provider->setClientResolver(function ($id) {
                // Not allowed yet.
            });

            return $provider;
        });
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        //
    }


}
