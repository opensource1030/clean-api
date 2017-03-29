<?php

namespace WA\Testing\Auth;

use WA\Auth\Login;
use TestCase;
use DB;

/**
 * MainHandler - Gets the event received by the Single Sign On.
 *
 * @author   Agustí Dosaiguas
 */

namespace WA\Events\Handlers\Saml2;

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
        // CREATE CLASS INSTANCE
        $userForm = app()->make('WA\Services\Form\User\UserForm');
        $handler = new \WA\Events\Handlers\Saml2\MainHandler($userForm);

        // CREATE REFLECTOR & ACCESSIBLE
        $reflector = new ReflectionClass($handler);
        $method = $reflector->getMethod('parseRequestedInfoFromIdp');
        $method->setAccessible(true);

        // CREATE ARGUMENTS
        $userData['attributes']['http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress'][0] = 'correo@electronico.com';
        $userData['attributes']['http://schemas.xmlsoap.org/ws/2005/05/identity/claims/givenname'][0] = 'prueba';
        $userData['attributes']['http://schemas.xmlsoap.org/ws/2005/05/identity/claims/name'][0] = 'correo';
        $userData['attributes']['http://schemas.xmlsoap.org/ws/2005/05/identity/claims/upn'][0] = 'upn';
        $userData['attributes']['http://schemas.xmlsoap.org/ws/2005/05/identity/claims/surname'][0] = 'test';
        $userData['attributes']['http://schemas.xmlsoap.org/ws/2005/05/identity/claims/privatepersonalidentifier'][0] = 'privatepersonalidentifier';
        $userData['attributes']['http://schemas.xmlsoap.org/ws/2005/05/identity/claims/nameidentifier'][0] = 'nameidentifier';
        $userData['attributes']['http://schemas.xmlsoap.org/ws/2005/05/identity/claims/denyonlysid'][0] = 'denyonlysid';
        $userData['attributes']['http://schemas.xmlsoap.org/ws/2005/05/identity/claims/rsa'][0] = 'rsa';
        $userData['attributes']['http://schemas.xmlsoap.org/ws/2005/05/identity/claims/thumbprint'][0] = 'thumbprint';

        // CALL THE FUNCTION
        $return = $method->invokeArgs($handler, array($userData, 9));

        // ASSERTS
        $this->assertArrayHasKey('email', $return);
        $this->assertInternalType('string', $return['email']);
        $this->assertArrayHasKey('name', $return);
        $this->assertInternalType('string', $return['name']);
        $this->assertArrayHasKey('companyId', $return);
        $this->assertInternalType('integer', $return['companyId']);
        $this->assertArrayHasKey('isActive', $return);
        $this->assertInternalType('integer', $return['isActive']);
        $this->assertArrayHasKey('givenName', $return);
        $this->assertInternalType('string', $return['givenName']);
        $this->assertArrayHasKey('upn', $return);
        $this->assertInternalType('string', $return['upn']);
        $this->assertArrayHasKey('surname', $return);
        $this->assertInternalType('string', $return['surname']);
        $this->assertArrayHasKey('personalIdentifier', $return);
        $this->assertInternalType('string', $return['personalIdentifier']);
        $this->assertArrayHasKey('denyonlysid', $return);
        $this->assertInternalType('string', $return['denyonlysid']);
        $this->assertArrayHasKey('rsa', $return);
        $this->assertInternalType('string', $return['rsa']);
        $this->assertArrayHasKey('thumb', $return);
        $this->assertInternalType('string', $return['thumb']);
    }

    public function testCreateUserSSO()
    {
        // @TODOSAML2 : TEST IF WORKS.
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
        $config['sp']['entityId'] = 'http://clean.local/saml2/metadata';

        $config['sp']['assertionConsumerService']['url'] = 'http://clean.local/saml2/acs';

        $config['sp']['singleLogoutService']['url'] = 'http://clean.local/saml2/sls';

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
}
