<?php

namespace WA\Testing\Auth;

use WA\Auth\Login;
use TestCase;

/**
 * MainHandler - Gets the event received by the Single Sign On.
 *  
 * @author   Agustí Dosaiguas
 */

namespace WA\Events\Handlers\Saml2;

use Illuminate\Contracts\Events\Dispatcher;
use WA\DataStore\CarrierDestinationMap;
use WA\Events\Handlers\BaseHandler;

use Auth;
use WA\Events\PodcastWasPurchased;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Aacotroneo\Saml2\Events\Saml2LoginEvent;

use WA\DataStore\User\User;

use Cache;

use Carbon\Carbon;

use WA\Services\Form\User\UserForm;

use WA\Repositories\Company\CompanyInterface;
use WA\Repositories\User\UserInterface;
use WA\Repositories\Role\RoleInterface;
use WA\Repositories\Allocation\AllocationInterface;

use TestCase;
use ReflectionClass;
use OneLogin_Saml2_Auth;

class MainHandlerTest extends TestCase
{

    public function testSaml2LoginUser()
    {
        // @TODOSAML2 : TEST API.
    }


    public function testParseRequestedInfoFromIdp()
    {
        // CREATE CONSTANTS
        define('USER_EMAIL', 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/name');  
        define('USER_LASTNAME', 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/surname');  
        define('USER_FIRSTNAME', 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/givenname');  

        // CREATE CLASS INSTANCE
        $userForm = app()->make('WA\Services\Form\User\UserForm');
        $handler = new \WA\Events\Handlers\Saml2\MainHandler($userForm);

        // CREATE REFLECTOR & ACCESSIBLE
        $reflector = new ReflectionClass($handler);
        $method = $reflector->getMethod('parseRequestedInfoFromIdp');
        $method->setAccessible(true);

        // CREATE ARGUMENTS
        $userData['attributes']['http://schemas.xmlsoap.org/ws/2005/05/identity/claims/name'][0] = 'correo@electronico.com';
        $userData['attributes']['http://schemas.xmlsoap.org/ws/2005/05/identity/claims/name'][0][0] = 'correo';
        $userData['attributes']['http://schemas.xmlsoap.org/ws/2005/05/identity/claims/givenname'][0] = 'prueba';
        $userData['attributes']['http://schemas.xmlsoap.org/ws/2005/05/identity/claims/surname'][0] = 'test';
        $userData['attributes']['http://schemas.xmlsoap.org/ws/2005/05/identity/claims/name'][0] = 'supervisor@email.com';

        // CALL THE FUNCTION
        $return = $method->invokeArgs($handler, array($userData, 9));

        // ASSERTS
        $this->assertArrayHasKey('email', $return);
        $this->assertInternalType('string', $return['email']);
        $this->assertArrayHasKey('alternateEmail', $return);
        $this->assertInternalType('string', $return['alternateEmail']);
        $this->assertArrayHasKey('password', $return);
        $this->assertInternalType('string', $return['password']);
        $this->assertArrayHasKey('username', $return);
        $this->assertInternalType('string', $return['username']);
        $this->assertArrayHasKey('confirmation_code', $return);
        $this->assertInternalType('string', $return['confirmation_code']);
        $this->assertArrayHasKey('remember_token', $return);
        $this->assertInternalType('null', $return['remember_token']);
        $this->assertArrayHasKey('confirmed', $return);
        $this->assertInternalType('integer', $return['confirmed']);
        $this->assertArrayHasKey('firstName', $return);
        $this->assertInternalType('string', $return['firstName']);
        $this->assertArrayHasKey('alternateFirstName', $return);
        $this->assertInternalType('null', $return['alternateFirstName']);
        $this->assertArrayHasKey('lastName', $return);
        $this->assertInternalType('string', $return['lastName']);
        $this->assertArrayHasKey('supervisorEmail', $return);
        $this->assertInternalType('string', $return['supervisorEmail']);
        $this->assertArrayHasKey('companyUserIdentifier', $return);
        $this->assertInternalType('string', $return['companyUserIdentifier']);
        $this->assertArrayHasKey('isSupervisor', $return);
        $this->assertInternalType('integer', $return['isSupervisor']);
        $this->assertArrayHasKey('isValidator', $return);
        $this->assertInternalType('integer', $return['isValidator']);
        $this->assertArrayHasKey('isActive', $return);
        $this->assertInternalType('integer', $return['isActive']);
        $this->assertArrayHasKey('rgt ', $return);
        $this->assertInternalType('null', $return['rgt ']);
        $this->assertArrayHasKey('lft ', $return);
        $this->assertInternalType('null', $return['lft ']);
        $this->assertArrayHasKey('hierarchy', $return);
        $this->assertInternalType('null', $return['hierarchy']);
        $this->assertArrayHasKey('notes', $return);
        $this->assertInternalType('string', $return['notes']);
        $this->assertArrayHasKey('companyId', $return);
        $this->assertInternalType('integer', $return['companyId']);
        $this->assertArrayHasKey('syncId', $return);
        $this->assertInternalType('null', $return['syncId']);
        $this->assertArrayHasKey('supervisorId', $return);
        $this->assertInternalType('null', $return['supervisorId']);
        $this->assertArrayHasKey('externalId', $return);
        $this->assertInternalType('null', $return['externalId']);
        $this->assertArrayHasKey('approverId', $return);
        $this->assertInternalType('null', $return['approverId']);
        $this->assertArrayHasKey('deleted_at', $return);
        $this->assertInternalType('null', $return['deleted_at']);
        $this->assertArrayHasKey('created_at', $return);
        $this->assertInternalType('string', $return['created_at']);
        $this->assertArrayHasKey('updated_at', $return);
        $this->assertInternalType('null', $return['updated_at']);
        $this->assertArrayHasKey('defaultLocationId', $return);
        $this->assertInternalType('string', $return['defaultLocationId']);
        $this->assertArrayHasKey('defaultLang', $return);
        $this->assertInternalType('string', $return['defaultLang']);
        $this->assertArrayHasKey('departmentId', $return);
        $this->assertInternalType('null', $return['departmentId']);
        $this->assertArrayHasKey('identification', $return);
        $this->assertInternalType('string', $return['identification']);
        $this->assertArrayHasKey('notify', $return);
        $this->assertInternalType('integer', $return['notify']);
        $this->assertArrayHasKey('apiToken', $return);
        $this->assertInternalType('null', $return['apiToken']);
        $this->assertArrayHasKey('level', $return);
        $this->assertInternalType('integer', $return['level']);
    }

    public function testCreateUserSSO()
    {
        // @TODOSAML2 : TEST IF WORKS.
    }

    public function testCreateUserFacebookTest()
    {
        // CREATE CONSTANTS

        // CREATE CLASS INSTANCE
        $userForm = app()->make('WA\Services\Form\User\UserForm');
        $handler = new \WA\Events\Handlers\Saml2\MainHandler($userForm);

        // CREATE REFLECTOR & ACCESSIBLE
        $reflector = new ReflectionClass($handler);
        $method = $reflector->getMethod('createUserFacebookTest');
        $method->setAccessible(true);

        // CREATE ARGUMENTS

        // CALL THE FUNCTION
        $return = $method->invoke($handler);

        // ASSERTS
        $this->assertArrayHasKey('email', $return);
        $this->assertInternalType('string', $return['email']);
        $this->assertArrayHasKey('alternateEmail', $return);
        $this->assertInternalType('string', $return['alternateEmail']);
        $this->assertArrayHasKey('password', $return);
        $this->assertInternalType('string', $return['password']);
        $this->assertArrayHasKey('username', $return);
        $this->assertInternalType('string', $return['username']);
        $this->assertArrayHasKey('confirmation_code', $return);
        $this->assertInternalType('string', $return['confirmation_code']);
        $this->assertArrayHasKey('remember_token', $return);
        $this->assertInternalType('null', $return['remember_token']);
        $this->assertArrayHasKey('confirmed', $return);
        $this->assertInternalType('integer', $return['confirmed']);
        $this->assertArrayHasKey('firstName', $return);
        $this->assertInternalType('string', $return['firstName']);
        $this->assertArrayHasKey('alternateFirstName', $return);
        $this->assertInternalType('null', $return['alternateFirstName']);
        $this->assertArrayHasKey('lastName', $return);
        $this->assertInternalType('string', $return['lastName']);
        $this->assertArrayHasKey('supervisorEmail', $return);
        $this->assertInternalType('string', $return['supervisorEmail']);
        $this->assertArrayHasKey('companyUserIdentifier', $return);
        $this->assertInternalType('string', $return['companyUserIdentifier']);
        $this->assertArrayHasKey('isSupervisor', $return);
        $this->assertInternalType('integer', $return['isSupervisor']);
        $this->assertArrayHasKey('isValidator', $return);
        $this->assertInternalType('integer', $return['isValidator']);
        $this->assertArrayHasKey('isActive', $return);
        $this->assertInternalType('integer', $return['isActive']);
        $this->assertArrayHasKey('rgt ', $return);
        $this->assertInternalType('null', $return['rgt ']);
        $this->assertArrayHasKey('lft ', $return);
        $this->assertInternalType('null', $return['lft ']);
        $this->assertArrayHasKey('hierarchy', $return);
        $this->assertInternalType('null', $return['hierarchy']);
        $this->assertArrayHasKey('notes', $return);
        $this->assertInternalType('string', $return['notes']);
        $this->assertArrayHasKey('companyId', $return);
        $this->assertInternalType('integer', $return['companyId']);
        $this->assertArrayHasKey('syncId', $return);
        $this->assertInternalType('null', $return['syncId']);
        $this->assertArrayHasKey('supervisorId', $return);
        $this->assertInternalType('null', $return['supervisorId']);
        $this->assertArrayHasKey('externalId', $return);
        $this->assertInternalType('null', $return['externalId']);
        $this->assertArrayHasKey('approverId', $return);
        $this->assertInternalType('null', $return['approverId']);
        $this->assertArrayHasKey('deleted_at', $return);
        $this->assertInternalType('null', $return['deleted_at']);
        $this->assertArrayHasKey('created_at', $return);
        $this->assertInternalType('string', $return['created_at']);
        $this->assertArrayHasKey('updated_at', $return);
        $this->assertInternalType('null', $return['updated_at']);
        $this->assertArrayHasKey('defaultLocationId', $return);
        $this->assertInternalType('string', $return['defaultLocationId']);
        $this->assertArrayHasKey('defaultLang', $return);
        $this->assertInternalType('string', $return['defaultLang']);
        $this->assertArrayHasKey('departmentId', $return);
        $this->assertInternalType('null', $return['departmentId']);
        $this->assertArrayHasKey('identification', $return);
        $this->assertInternalType('string', $return['identification']);
        $this->assertArrayHasKey('notify', $return);
        $this->assertInternalType('integer', $return['notify']);
        $this->assertArrayHasKey('apiToken', $return);
        $this->assertInternalType('null', $return['apiToken']);
        $this->assertArrayHasKey('level', $return);
        $this->assertInternalType('integer', $return['level']);
    }

    public function testGetUuidFromRequestRelayState()
    {
        // @TODOSAML2 : NEED REQUEST
    }

    public function testGetUserDataFromSaml2User()
    {
        // CREATE CONSTANTS

        // CREATE CLASS INSTANCE
        $userForm = app()->make('WA\Services\Form\User\UserForm');
        $handler = new \WA\Events\Handlers\Saml2\MainHandler($userForm);

        // CREATE REFLECTOR & ACCESSIBLE
        $reflector = new ReflectionClass($handler);
        $method = $reflector->getMethod('getUserDataFromSaml2User');
        $method->setAccessible(true);

        // CREATE ARGUMENTS
        $config['sp']['entityId'] = 'http://clean.local/saml2/metadata?idCompany=9';

        $config['sp']['assertionConsumerService']['url'] = 'http://clean.local/saml2/acs?idCompany=9';

        $config['sp']['singleLogoutService']['url'] = 'http://clean.local/saml2/sls?idCompany=9';

        // Saml2_Settings Information.
        $config['idp']['entityId'] = 
                  'https://sts.windows.net/d6f6af8c-1dd5-4480-afe3-53aaa025a273/';

        $config['idp']['singleSignOnService']['url'] = 
                  'https://login.microsoftonline.com/d6f6af8c-1dd5-4480-afe3-53aaa025a273/saml2';

        $config['idp']['singleSignOnService']['binding'] = 
                  'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect';

        $config['idp']['singleLogoutService']['url'] = 
                  'https://login.microsoftonline.com/d6f6af8c-1dd5-4480-afe3-53aaa025a273/saml2';

        $config['idp']['singleLogoutService']['binding'] = 
                  'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect';

        $config['idp']['x509cert'] = 
                  'MIIC4jCCAcqgAwIBAgIQQNXrmzhLN4VGlUXDYCRT3zANBgkqhkiG9w0BAQsFADAtMSswKQYDVQQDEyJhY2NvdW50cy5hY2Nlc3Njb250cm9sLndpbmRvd3MubmV0MB4XDTE0MTAyODAwMDAwMFoXDTE2MTAyNzAwMDAwMFowLTErMCkGA1UEAxMiYWNjb3VudHMuYWNjZXNzY29udHJvbC53aW5kb3dzLm5ldDCCASIwDQYJKoZIhvcNAQEBBQADggEPADCCAQoCggEBALyKs/uPhEf7zVizjfcr/ISGFe9+yUOqwpel38zgutvLHmFD39E2hpPdQhcXn4c4dt1fU5KvkbcDdVbP8+e4TvNpJMy/nEB2V92zCQ/hhBjilwhF1ETe1TMmVjALs0KFvbxW9ZN3EdUVvxFvz/gvG29nQhl4QWKj3x8opr89lmq14Z7T0mzOV8kub+cgsOU/1bsKqrIqN1fMKKFhjKaetctdjYTfGzVQ0AJAzzbtg0/Q1wdYNAnhSDafygEv6kNiquk0r0RyasUUevEXs2LY3vSgKsKseI8ZZlQEMtE9/k/iAG7JNcEbVg53YTurNTrPnXJOU88mf3TToX14HpYsS1ECAwEAATANBgkqhkiG9w0BAQsFAAOCAQEAfolx45w0i8CdAUjjeAaYdhG9+NDHxop0UvNOqlGqYJexqPLuvX8iyUaYxNGzZxFgGI3GpKfmQP2JQWQ1E5JtY/n8iNLOKRMwqkuxSCKJxZJq4Sl/m/Yv7TS1P5LNgAj8QLCypxsWrTAmq2HSpkeSk4JBtsYxX6uhbGM/K1sEktKybVTHu22/7TmRqWTmOUy9wQvMjJb2IXdMGLG3hVntN/WWcs5w8vbt1i8Kk6o19W2MjZ95JaECKjBDYRlhG1KmSBtrsKsCBQoBzwH/rXfksTO9JoUYLXiW0IppB7DhNH4PJ5hZI91R8rR0H3/bKkLSuDaKLWSqMhozdhXsIIKvJQ==';

        $OneLogin_Saml2_Auth = new OneLogin_Saml2_Auth($config);
        $saml2Auth = new \Aacotroneo\Saml2\Saml2Auth($OneLogin_Saml2_Auth);
        $user = $saml2Auth->getSaml2User();
        $event = new \Aacotroneo\Saml2\Events\Saml2LoginEvent($user);

        // CALL THE FUNCTION
        $return = $method->invokeArgs($handler, array($event));
        
        // ASSERTS
        $this->assertArrayHasKey('id', $return);
        $this->assertInternalType('null', $return['id']);
        $this->assertArrayHasKey('attributes', $return);
        $this->assertInternalType('array', $return['attributes']);
        $this->assertArrayHasKey('assertion', $return);
        $this->assertInternalType('null', $return['assertion']);
    }

    public function testGetEmailFromUserData()
    {
        // CREATE CONSTANTS

        // CREATE CLASS INSTANCE
        $userForm = app()->make('WA\Services\Form\User\UserForm');
        $handler = new \WA\Events\Handlers\Saml2\MainHandler($userForm);

        // CREATE REFLECTOR & ACCESSIBLE
        $reflector = new ReflectionClass($handler);
        $method = $reflector->getMethod('getEmailFromUserData');
        $method->setAccessible(true);

        // CREATE ARGUMENTS
        $userData['attributes']['http://schemas.xmlsoap.org/ws/2005/05/identity/claims/name'][0] = 'correo@electronico.com';

        // CALL THE FUNCTION
        $returnUserData = $method->invokeArgs($handler, array($userData, 7));
        $returnCompanyId = $method->invokeArgs($handler, array($userData, 21));

        // ASSERTS
        $this->assertStringStartsWith('correo@electronico.com', $returnUserData);
        $this->assertStringStartsWith('dariana.donnelly@example.com', $returnCompanyId);
    }
}