<?php

namespace WA\Testing\Auth;

use WA\Auth\Login;
use WA\Testing\TestCase;

class LoginTest extends TestCase
{

    protected $credentials;

    public function setUp()
    {
        $this->markTestSkipped('Must be refactored to utilize Confide instead of Sentry');

        parent::setUp();
        $this->credentials = ['email' => 'something@something.com', 'password' => 'test123'];

    }


    public function testLoginInUser()
    {
        $mockedSentry = $this->mock('Cartalyst\Sentinel\Sentinel');
        $mockedSentry->shouldReceive('authenticateAndRemember')
                     ->with($this->credentials)
                     ->once()
                     ->andReturn(true);
        $testObject = new Login($mockedSentry);
        $this->assertTrue($testObject->login($this->credentials));
    }

    public function testLoginInUserWithoutRemember()
    {
        $mockedSentry = $this->mock('Cartalyst\Sentinel\Sentinel');
        $mockedSentry->shouldReceive('authenticate')
                     ->with($this->credentials)
                     ->once()
                     ->andReturn(true);

        $testObject = new Login($mockedSentry);
        $this->assertTrue($testObject->login($this->credentials, false));
    }

    /**
     * @expectedException Exception
     */
    public function testThrowsAnExpectionWithWrongCredentials()
    {
        $mockedSentry = $this->mock('Cartalyst\Sentinel\Sentinel');
        $mockedSentry->shouldReceive('authenticate')
                     ->with($this->credentials)
                     ->once()
                     ->andThrow(new \Exception);
        $testObject = new Login($mockedSentry);
        $testObject->login($this->credentials, false);
    }
}
