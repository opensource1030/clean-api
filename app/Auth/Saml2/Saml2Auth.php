<?php

namespace WA\Auth\Saml2;

use Aacotroneo\Saml2\Saml2Auth as Saml2A;

class Saml2Auth extends Saml2A
{

    /**
     * @var \OneLogin_Saml2_Auth
     */

    /**
     * Initiate a saml2 login flow. It will redirect! Before calling this, check if user is
     * authenticated (here in saml2). That would be true when the assertion was received this request.
     */
    function login($returnTo = null)
    {
        $auth = $this->auth;
        $parameters = array();
        
        $forceAuthn = false;
        $isPassive = false;
        $stay = true;  //If $stay is True, it return a string with the SLO URL + LogoutRequest + parameters
        $setNameIdPolicy = true;

        return $auth->login($returnTo, $parameters, $forceAuthn, $isPassive, $stay, $setNameIdPolicy);
    }
}