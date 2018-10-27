<?php

namespace WA\Providers;

use Dusterio\LumenPassport\Console\Commands\Purge;
use Illuminate\Database\Connection;
use Dusterio\LumenPassport\PassportServiceProvider as PassServiceProvider;

/**
 * Class PassportServiceProvider
 * @package App\Providers
 */
class PassportServiceProvider extends PassServiceProvider
{
    /**
     * Register the routes needed for managing personal access tokens.
     *
     * @return void
     */
    public function forPersonalAccessTokens()
    {
        $this->app->group(['middleware' => ['auth']], function () {
            $this->app->get('/oauth/scopes', [
                'uses' => '\Laravel\Passport\Http\Controllers\ScopeController@all',
            ]);

            $this->app->get('/oauth/personal-access-tokens', [
                'uses' => '\Laravel\Passport\Http\Controllers\PersonalAccessTokenController@forUser',
            ]);

            $this->app->post('/oauth/personal-access-tokens', [
                'uses' => '\WA\Auth\PersonalAccessTokenController@store',
            ]);

            $this->app->delete('/oauth/personal-access-tokens/{token_id}', [
                'uses' => '\Laravel\Passport\Http\Controllers\PersonalAccessTokenController@destroy',
            ]);
        });
    }
}
