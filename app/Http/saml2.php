<?php

/*
|--------------------------------------------------------------------------
| Application Routes of SAML2
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
| @author: Agustí Dosaiguas
|
*/
Route::get('/doSSO/{email}', ['as' => 'dosso','uses' => 'Auth\SSO@loginRequest']);
Route::get('/doSSO/login/{uuid}', ['as' => 'dosso_login', 'uses' => 'Auth\SSO@loginUser']);