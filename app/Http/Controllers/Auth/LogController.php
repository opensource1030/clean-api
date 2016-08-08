<?php

namespace WA\Http\Controllers\Auth;

use WA\Http\Controllers\BaseController;
use Auth;
use Aacotroneo\Saml2\Events\Saml2LoginEvent;
use Log;
use Event;

class LogController extends BaseController
{
    public function loggedIn()
    {
        Log::info( 'WA\Http\Controllers\Auth\LogController loggedIn : ');        
        return view('logged');        
    }

    public function loggedOut()
    {
        Log::info( 'WA\Http\Controllers\Auth\LogController loggedOut');
    }

    public function loginError()
    {
        Log::info( 'WA\Http\Controllers\Auth\LogController loginError');
    }
}