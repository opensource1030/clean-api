<?php


namespace WA\Providers;


use Dingo\Api\Transformer\Adapter\Fractal;
use Illuminate\Support\ServiceProvider;
use League\Fractal\Manager;
use League\Fractal\Serializer\JsonApiSerializer;

class FractalServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('League\Fractal\Manager', function () {
            $fractal = new Manager();
            $base_url = env('API_PREFIX', 'api.wirelessanalytics.com');
            $serializer = new JsonApiSerializer($base_url);

            $fractal->setSerializer($serializer);

            return $fractal;
        });

        $this->app->bind('Dingo\Api\Transformer\Adapter\Fractal', function () {
            $fractal = app()->make('League\Fractal\Manager');

            return new Fractal($fractal);
        });
    }
}