<?php

namespace WA\DataStore;

use WA\DataStore\BaseDataStore;

/**
 * Class EmailNotifications
 *
 * @mixin \Eloquent
 */
class EmailNotifications extends BaseDataStore
{
    protected $table = 'email_notifications';
    protected $fillable = ['user_id', 'category_id', 'data', 'read', 'sent_on', 'created_at', 'updated_at'];

}