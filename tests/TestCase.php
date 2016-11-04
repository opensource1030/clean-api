<?php


abstract class TestCase extends Laravel\Lumen\Testing\TestCase
{
    protected $baseUrl;

    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';
        $this->baseUrl = 'http://'.getenv('API_DOMAIN');

        return $app;
    }
}
