<?php

namespace WA\DataStore\Order;

use WA\DataStore\BaseDataStore;

/**
 * Class Order.
 */
class Order extends BaseDataStore
{
    protected $table = 'orders';

    protected $fillable = [
            'status',
            'userId',
            'packageId',
            'deviceId',
            'serviceId',
            'updated_at', ];

  /**
   * Get all the owners for the order.
   *
   * @return \Illuminate\Database\Eloquent\Relations\MorphTo
   */
  public function owner()
  {
      return $this->morphTo();
  }

    /**
     * Get the transformer instance.
     *
     * @return OrderTransformer
     */
    public function getTransformer()
    {
        return new OrderTransformer();
    }
}