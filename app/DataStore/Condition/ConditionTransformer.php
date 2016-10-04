<?php

namespace WA\DataStore\Condition;

use League\Fractal\Resource\Collection as ResourceCollection;
use League\Fractal\Resource\Item as ResourceItem;
use League\Fractal\TransformerAbstract;

/**
 * Class ConditionTransformer
 *
 */
class ConditionTransformer extends TransformerAbstract
{
    /**
     * @param Condition $condition
     *
     * @return array
     */
    public function transform(Condition $condition)
    {
        return [

            'id' => (int)$condition->id,
            'typeCond' => $condition->typeCond,
            'name' => $condition->name,
            'condition' => $condition->condition,
            'value' => $condition->value,
            'created_at' => $condition->created_at,
            'updated_at' => $condition->updated_at,
        ];
    }
}