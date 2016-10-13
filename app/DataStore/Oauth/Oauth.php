<?php

namespace WA\DataStore\Oauth;

use WA\DataStore\BaseDataStore;

/**
 * WA\DataStore\Company\CompanySaml2
 *
 * @mixin \Eloquent
 */
class Oauth extends BaseDataStore
{
    protected $table = 'oauth_clients';

    protected $fillable = ['id', 'secret', 'name'];
}