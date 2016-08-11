<?php

require_once __DIR__ . '/../vendor/autoload.php';

try {
    (new Dotenv\Dotenv(__DIR__ . '/../'))->load();
} catch (Dotenv\Exception\InvalidPathException $e) {
    //
}

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Here we will load the environment and create the application instance
| that serves as the central piece of this framework. We'll use this
| application as an "IoC" container and router for this framework.
|
*/

$app = new Laravel\Lumen\Application(
    realpath(__DIR__ . '/../')
);

$app->withFacades();

$app->withEloquent();

/*
|--------------------------------------------------------------------------
| Register Container Bindings
|--------------------------------------------------------------------------
|
| Now we will register a few bindings in the service container. We will
| register the exception handler and the console kernel. You may add
| your own bindings here if you like or you can make another file.
|
*/

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    WA\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    WA\Console\Kernel::class
);



/*
|--------------------------------------------------------------------------
| Register Middleware
|--------------------------------------------------------------------------
|
| Next, we will register the middleware with the application. These can
| be global middleware that run before and after each request into a
| route or middleware that'll be assigned to some specific routes.
|
*/

$app->middleware([
    \LucaDegasperi\OAuth2Server\Middleware\OAuthExceptionHandlerMiddleware::class
]);

$app->routeMiddleware([
    'auth' => WA\Http\Middleware\Authenticate::class,
    'role' => 'Zizaco\Entrust\Middleware\EntrustRole',
    'permission' => 'Zizaco\Entrust\Middleware\EntrustPermission',
    'ability' => 'Zizaco\Entrust\Middleware\EntrustAbility',
    'oauth-user-instance' => \WA\Http\Middleware\OAuth2UserInstance::class,
    'oauth' => \LucaDegasperi\OAuth2Server\Middleware\OAuthMiddleware::class,
    'oauth-client' => \LucaDegasperi\OAuth2Server\Middleware\OAuthClientOwnerMiddleware::class,
    'oauth-user' => \LucaDegasperi\OAuth2Server\Middleware\OAuthUserOwnerMiddleware::class,
    'check-authorization-params' => \LucaDegasperi\OAuth2Server\Middleware\CheckAuthCodeRequestMiddleware::class,
]);

/*
|--------------------------------------------------------------------------
| Register Service Providers
|--------------------------------------------------------------------------
|
| Here we will register all of the application's service providers which
| are used to bind services into the container. Service providers are
| totally optional, so you are not required to uncomment this line.
|
*/

// $app->register(WA\Providers\AppServiceProvider::class);
//$app->register(WA\Providers\AuthServiceProvider::class);
// $app->register(WA\Providers\EventServiceProvider::class);
$app->register(Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
$app->register(\Culpa\CulpaServiceProvider::class);
$app->register(\Dingo\Api\Provider\LumenServiceProvider::class);
$app->register(LucaDegasperi\OAuth2Server\OAuth2ServerServiceProvider::class);
$app->register(LucaDegasperi\OAuth2Server\Storage\FluentStorageServiceProvider::class);
$app->register(\WA\Providers\RepositoriesServiceProviders::class);
$app->register(\WA\Providers\AppServiceProvider::class);
$app->register(\Illuminate\Auth\Passwords\PasswordResetServiceProvider::class);
$app->register(\Illuminate\Mail\MailServiceProvider::class);
$app->register(\WA\Providers\FormServiceProvider::class);
$app->register(\WA\Providers\EventServiceProvider::class);
$app->register('Dingo\Api\Provider\LumenServiceProvider');
$app->register(\WA\Providers\FractalServiceProvider::class);
$app->register(\WA\Providers\OAuthServiceProvider::class);

/*
|--------------------------------------------------------------------------
| Register Configurations
|--------------------------------------------------------------------------
|
*/
$app->configure('app');
$app->configure('api');
$app->configure('services');
$app->configure('mail');

// @TODOSAML2: saml2_settings must be loaded before its serviceProvider
$app->configure('saml2_settings');

$app->register(\WA\Providers\Saml2ServiceProvider::class);
/*
|--------------------------------------------------------------------------
| Load The Application Routes
|--------------------------------------------------------------------------
|
| Next we will include the routes file so that they can all be added to
| the application. This will provide all of the URLs the application
| can respond to, as well as the controllers that may handle them.
|
*/


$app->group(['namespace' => 'WA\Http\Controllers'], function ($app) {
    require __DIR__ . '/../app/Http/routes.php';
});

/*
|--------------------------------------------------------------------------
| Aliases
|--------------------------------------------------------------------------
|
*/
class_alias('Illuminate\Support\Facades\Response', 'Response');
class_alias('Illuminate\Support\Facades\Config', 'Config');
class_alias(\LucaDegasperi\OAuth2Server\Facades\Authorizer::class, 'Authorizer');
class_alias(\Webpatser\Uuid\Uuid::class, 'Uuid');
class_alias(\Aacotroneo\Saml2\Facades\Saml2Auth::class, 'Saml2');
class_alias('Illuminate\Support\Facades\Request', 'Request');

return $app;
