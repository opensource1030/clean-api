<?php


use Laravel\Lumen\Testing\DatabaseTransactions;


class BaseApiTest extends TestCase
{

    public function testCanCallHome()
    {
        $this->json('GET', '/')
            ->seeJson([
                'app_name' => 'CLEAN Platform',
                //'app_version' => $this->app->version()
            ]);
    }


}