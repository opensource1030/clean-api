<?php


use Laravel\Lumen\Testing\DatabaseTransactions;


class BaseApiTest extends TestCase
{

    protected $baseUrl = 'http://clean.api';


    public function testCanCallHome()
    {
        $this->json('GET', '/')
            ->seeJson([
                'app_name' => 'clean',
                'app_version' => $this->app->version()
            ]);
    }


}