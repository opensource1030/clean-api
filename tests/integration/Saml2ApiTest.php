<?php

namespace WA\Testing\Auth;

use Laravel\Lumen\Testing\DatabaseMigrations;
use WA\Auth\Login;
use TestCase;
use Cache;

class Saml2ApiTest extends TestCase
{
    use DatabaseMigrations;

    public function testApiDoSSOEmailRegister()
    {
         
        // CREATE ARGUMENTS
        $emailRegister = 'dev@algo.com';

        // CALL THE API ROUTE + ASSERTS
        $returnRegister = $this->json('GET', 'doSSO/'.$emailRegister)->seeJson([
            'error' => 'User Not Found, Register Required',
            'message' => 'Please, register a new user.',
        ]);
    }

    public function testApiDoSSOEmailPassword()
    {

        // CREATE USER
        $user = factory(\WA\DataStore\User\User::class)->create()->email;

        // CALL THE API ROUTE + ASSERTS
        $returnPassword = $this->json('GET', 'doSSO/'.$user)->seeJson([
            'error' => 'User Found, Password Required',
            'message' => 'Please, enter your password.',
        ]);
    }

    public function testApiDoSSOEmailMicrosoftFail()
    {

        // CREATE ARGUMENTS
        $emailMicrosoft = 'dev@wirelessanalytics.com';

        $company = factory(\WA\DataStore\Company\Company::class)->create()->id;
        $companyDomains = factory(\WA\DataStore\Company\CompanyDomains::class)->create(['companyId' => $company]);
        $companySaml2 = factory(\WA\DataStore\Company\CompanySaml2::class)->create(['companyId' => $company]);
        $user = factory(\WA\DataStore\User\User::class)->create(['email' => $emailMicrosoft, 'companyId' => $company]);

        // CALL THE API ROUTE + ASSERTS
        $returnMicrosoft = $this->call('GET', 'doSSO/'.$emailMicrosoft, array(), array(), array(), array(), array());
        $returnMicrosoftArray = json_decode($returnMicrosoft->content(), true);

        $this->assertArrayHasKey('error', $returnMicrosoftArray);
        $this->assertArrayHasKey('message', $returnMicrosoftArray);
        $this->assertStringStartsWith('URL Not Found', $returnMicrosoftArray['error']);
        $this->assertStringStartsWith('Url to redirect not found.', $returnMicrosoftArray['message']);
    }

    public function testApiDoSSOEmailMicrosoftSaml2()
    {

        // CREATE ARGUMENTS
        $emailMicrosoft = 'dev@wirelessanalytics.com';
        $redirectToUrl = 'http://google.es';

        $company = factory(\WA\DataStore\Company\Company::class)->create()->id;
        $companyDomains = factory(\WA\DataStore\Company\CompanyDomains::class)->create(['companyId' => $company]);
        $companySaml2 = factory(\WA\DataStore\Company\CompanySaml2::class)->create(['companyId' => $company]);
        $user = factory(\WA\DataStore\User\User::class)->create(['email' => $emailMicrosoft, 'companyId' => $company]);

        // CALL THE API ROUTE + ASSERTS
        $returnMicrosoft = $this->call('GET', 'doSSO/'.$emailMicrosoft.'?redirectToUrl='.$redirectToUrl, array(), array(), array(), array(), array());
        $returnMicrosoftArray = json_decode($returnMicrosoft->content(), true);
        $this->assertArrayHasKey('data', $returnMicrosoftArray);
        $this->assertArrayHasKey('redirectUrl', $returnMicrosoftArray['data']);
        $this->assertStringStartsWith('https://login.microsoftonline.com', $returnMicrosoftArray['data']['redirectUrl']);
    }

    public function testApiDoSSOEmailNoValid()
    {
       
        // CREATE ARGUMENTS
        $emailNoValid = 'dev';

        // CALL THE API ROUTE + ASSERTS
        $returnNoValid = $this->json('GET', 'doSSO/'.$emailNoValid)->seeJson([
            'error' => 'Invalid Email',
            'message' => 'Please, enter a valid Email Address.',
        ]);
    }

    public function testApiDoSSOLoginUuid()
    {

        
        // CREATE ARGUMENTS ERROR
        $uuid = 'siriondevelopers';

        // CALL THE API ROUTE WITHOUT LARAVEL USER -> ERROR
        $returnRegister = $this->json('GET', 'doSSO/login/'.$uuid)->seeJson([
            'error' => 'Required User',
            'message' => 'Please, user is not available now, try again later.',
        ]);

        // CREATE ARGUMENTS SUCCESS
        $laravelUser['attributes']['id'] = 1;
        Cache::put('saml2user_'.$uuid, $laravelUser, 1);

        // CALL THE API ROUTE + ASSERTS
        $returnRegister = $this->json('GET', 'doSSO/login/'.$uuid)->seeJson([
            'success' => 'User Successfully Logged',
            'uuid' => $uuid,
        ]);
    }
}
