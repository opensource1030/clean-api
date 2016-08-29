<?php

namespace WA\DataStore\Order;

use WA\DataStore\BaseDataStore;
use WA\DataStore\Order\OrderTransformer;

/**
 * Class Order
 *
 * @package WA\DataStore\Order
 */
class Order extends BaseDataStore
{
    protected  $table = 'order';

    protected $fillable = array('status', 'created_at', 'idEmployee', 'idPackage');


    /**
     * Get all the owners for the order
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
  public function owner()
  {
      return $this->morphTo();
  }

    /**
     * Get the transformer instance
     *
     * @return OrderTransformer
     */
    public function getTransformer()
    {
        return new OrderTransformer();
    }

}