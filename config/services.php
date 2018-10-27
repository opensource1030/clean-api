<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, Mandrill, and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => '',
        'secret' => '',
    ],

    'mandrill' => [
        'secret' => '',
    ],

    'stripe' => [
        'model'  => WA\DataStore\User\User::Class,
        'secret_key' => 'sk_test_4aaF9EC7BvccVzCkRcpkma6g',
        'public_key' => 'pk_test_4aaFaJSywZQzdiWTtv7ihQXx'

    ],

];