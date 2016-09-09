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

/*
PHPUnit 5.5.0 by Sebastian Bergmann and contributors.

F                                                                   1 / 1 (100%)

Time: 547 ms, Memory: 16.00MB

There was 1 failure:

1) BaseApiTest::testCanCallHome
Invalid JSON was returned from the route. Perhaps an exception was thrown?

/var/www/clean/vendor/laravel/lumen-framework/src/Testing/Concerns/MakesHttpRequests.php:271
/var/www/clean/vendor/laravel/lumen-framework/src/Testing/Concerns/MakesHttpRequests.php:208
/var/www/clean/tests/integration/BaseApiTest.php:18

FAILURES!
Tests: 1, Assertions: 0, Failures: 1.

*/
