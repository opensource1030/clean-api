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
            return redirect(config('saml2_settings.errorRoute'));
        }

        $user = $this->saml2Auth->getSaml2User();
        event(new Saml2LoginEvent($user));

        $relayState = app('request')->input('RelayState');
        $url = app('Illuminate\Routing\UrlGenerator');
        if ($relayState && $url->full() != $relayState) {
            return redirect($relayState);
        } else {
            return redirect(config('saml2_settings.loginRoute'));
        }
    }
}