<?php

namespace WA\DataStore\Condition;

use WA\DataStore\BaseDataStore;
use WA\DataStore\Order\OrderTransformer;

/**
 * Class Order
 *
 * @package WA\DataStore\Order
 */
class ConditionField extends BaseDataStore
{
    protected  $table = 'condition_fields';

    protected $fillable = [
            'typeField',
            'field'
        ];

    public $timestamps = false;

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
        return new ConditionFieldTransformer();
    }
}