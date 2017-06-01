<?php

namespace WA\DataStore\User;

use WA\DataStore\BaseDataStore;

class UserRole extends BaseDataStore
{
    protected $table = 'role_user';
    protected $fillable = ['user_id', 'role_id'];
}