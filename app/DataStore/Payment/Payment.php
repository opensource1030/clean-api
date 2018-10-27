<?php

namespace WA\DataStore\Payment;

use WA\DataStore\BaseDataStore;

/**
 * Class Address.
 */
class Payment extends BaseDataStore
{
    protected $table = 'payments';

    protected $fillable = [
            'success',
            'details',
            'transactionId',
            'userId',
            'orderId' ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->hasMany('WA\DataStore\User\User', 'userId');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function orders()
    {
        return $this->hasMany('WA\DataStore\Order\Order', 'orderId');
    }

    /**
     * Get the transformer instance.
     *
     * @return AddressTransformer
     */
    public function getTransformer()
    {
        return new PaymentTransformer();
    }
}
