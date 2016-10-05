<?php

namespace WA\DataStore\Condition;

use WA\DataStore\BaseDataStore;
use WA\DataStore\Order\OrderTransformer;

/**
 * Class Order
 *
 * @package WA\DataStore\Order
 */
class Condition extends BaseDataStore
{
    protected  $table = 'conditions';

    protected $fillable = [
            'typeCond',
            'name',
            'condition',
            'value',
        ];

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
        return new ConditionTransformer();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function packages()
    {
        return $this->belongsToMany('WA\DataStore\Package\Package', 'package_conditions', 'packageId', 'conditionsId');
    }
}