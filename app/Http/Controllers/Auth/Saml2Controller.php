<?php

namespace WA\Http\Controllers\Auth;

use Aacotroneo\Saml2\Http\Controllers\Saml2Controller as Saml2C;

use Aacotroneo\Saml2\Events\Saml2LoginEvent;
use Aacotroneo\Saml2\Saml2Auth;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

use Log;
use Event;

class Saml2Controller extends Saml2C
{
    public function acs()
    {
        $errors = $this->saml2Auth->acs();

        if (!empty($errors)) {
            Log::error('Saml2 error', $errors);
            //session()->flash('saml2_error', $errors);
            return redirect(config('saml2_settings.errorRoute'));
        }

        $user = $this->saml2Auth->getSaml2User();
        event(new Saml2LoginEvent($user));
        // @TODOSAML2: Modified Aacotroneo\Saml2\Saml2User@getIntendedUrl
        // CHANGED: $url = app('Illuminate\Contracts\Routing\UrlGenerator');
        // FOR TEST: $url = app('Illuminate\Routing\UrlGenerator'); 
        // FINAL SOLUTION: USE THE CONTENT OF THE GETINTENDEDURL FUNCTION DIRECTLY.

        $relayState = app('request')->input('RelayState'); //just this request
        $url = app('Illuminate\Routing\UrlGenerator');
        if ($relayState && $url->full() != $relayState) {
            //echo $relayState;
            return redirect($relayState);
        } else {
            //echo $relayState;
            return redirect(config('saml2_settings.loginRoute'));
        }
    }
}