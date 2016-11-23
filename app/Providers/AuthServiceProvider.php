<?php

namespace WA\Providers;

use Laravel\Passport\PassportServiceProvider;
use WA\Auth\BearerTokenResponse;
use League\OAuth2\Server\AuthorizationServer;

class AuthServiceProvider extends PassportServiceProvider
{
    /**
     * Make the authorization service instance.
     *
     * @return AuthorizationServer
     */
    public function makeAuthorizationServer()
    {
        return new AuthorizationServer(
            $this->app->make(\Laravel\Passport\Bridge\ClientRepository::class),
            $this->app->make(\Laravel\Passport\Bridge\AccessTokenRepository::class),
            $this->app->make(\Laravel\Passport\Bridge\ScopeRepository::class),
            'file://'.\Laravel\Passport\Passport::keyPath('oauth-private.key'),
            'file://'.\Laravel\Passport\Passport::keyPath('oauth-public.key'),
            new BearerTokenResponse()
        );
    }
}
