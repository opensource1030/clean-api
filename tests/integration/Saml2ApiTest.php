<?php

//use WA\Auth\Login;
//use TestCase;
//use Cache;
//use Log;

class Saml2ApiTest extends \TestCase
{
    use \Laravel\Lumen\Testing\DatabaseMigrations;

    public function testApiDoSSOInvalidEmail()
    {
        // CREATE ARGUMENTS
        $emailInvalidEmail = 'dev@a';

        // CALL THE API ROUTE + ASSERTS
        $returnNoValid = $this->json('GET', 'doSSO/'.$emailInvalidEmail)->seeJson([
            'error' => 'Invalid Email',
            'message' => 'Please, enter a valid Email Address.',
        ]);
    }

    public function testApiDoSSOCompanyNotExist() 
    {
        // CREATE ARGUMENTS
        $emailCompanyNotExist = 'dev@testing.com';

        // CALL THE API ROUTE + ASSERTS
        $returnNoValid = $this->json('GET', 'doSSO/'.$emailCompanyNotExist)->seeJson([
            'error' => 'Company Not Found',
            'message' => 'Please, contact with the administrator of your company.',
        ]);
    }

    public function testApiDoSSOUserNotExist()
    {
        $emailUserNotExist = 'dev@testing.com';
        $redirectToUrl = 'http://google.es';

        // CREATE COMPANY and COMPANY DOMAIN
        $companyId = factory(\WA\DataStore\Company\Company::class)->create()->id;
        $companyDomains = factory(\WA\DataStore\Company\CompanyDomains::class)->create(['companyId' => $companyId, 'domain' => 'testing.com']);

        // CALL THE API ROUTE + ASSERTS
        $returnPassword = $this->json('GET', 'doSSO/'.$emailUserNotExist.'?redirectToUrl='.$redirectToUrl)->seeJson([
            'error' => 'User Not Found, Register Required',
            'message' => 'Please, register a new user.',
        ]);
    }

    public function testApiDoSSOEmailPassword()
    {
        $email = 'dev@withpassword.com';
        $redirectToUrl = 'http://google.es';

        // CREATE COMPANY and COMPANY DOMAIN
        $company = factory(\WA\DataStore\Company\Company::class)->create()->id;
        $companyDomains = factory(\WA\DataStore\Company\CompanyDomains::class)->create(['companyId' => $company, 'domain' => 'withpassword.com']);
        // CREATE USER
        $user = factory(\WA\DataStore\User\User::class)->create(['email' => $email, 'companyId' => $company])->email;

        // CALL THE API ROUTE + ASSERTS
        $returnPassword = $this->json('GET', 'doSSO/'.$user.'?redirectToUrl='.$redirectToUrl)->seeJson([
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

    /**
    * @runInSeparateProcess
    * @preserveGlobalState disabled
    * @group need-review
    *
    * The problem is that when not run in a separate process it throws a
    * headers already sent exception but, when run in a separate process,
    * disabling preserve global state, you get an unserialize error.
    * We might need to just assert the headers already sent exception.
    */
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
