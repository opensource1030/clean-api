<?php

namespace WA\DataStore\Order;

use WA\DataStore\BaseDataStore;

/**
 * Class Order.
 */
class OrderJob extends BaseDataStore
{
    protected $table = 'order_jobs';

    protected $fillable = [
            'orderId',
            'statusBefore',
            'statusAfter'
        ];

    /**
     * Get the transformer instance.
     *
     * @return OrderTransformer
     */
    public function getTransformer()
    {
        return new OrderJobTransformer();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function orders()
    {
        return $this->belongsTo('WA\DataStore\Order\Order', 'orderId');
    }
}
