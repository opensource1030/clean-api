<?php

namespace WA\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Routing\Router;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to the controller routes in your routes file.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'WA\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @param \Illuminate\Routing\Router $router
     */
    public function boot(Router $router)
    {
        parent::boot($router);

        //
    }

    /**
     * Define the routes for the application.
     *
     * @param \Illuminate\Routing\Router $router
     */
    public function map(Router $router)
    {
        $router->group(['namespace' => $this->namespace], function ($router) {
            require app_path('/Http/Routes/pages.php');
            require app_path('/Http/Routes/api.php');
            require app_path('/Http/Routes/datatables.php');
           // require app_path('/Http/Routes/errors.php');
            require app_path('/Http/Routes/admin.php');
            require app_path('/Http/Routes/dev.php');
            require app_path('/Http/Routes/saml2.php');
        });
    }
}
