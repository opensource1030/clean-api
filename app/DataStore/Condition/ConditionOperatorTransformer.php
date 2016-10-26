<?php

namespace WA\DataStore\Condition;

use League\Fractal\TransformerAbstract;

/**
 * Class ConditionTransformer.
 */
class ConditionOperatorTransformer extends TransformerAbstract
{
    /**
     * @param ConditionOperator $conditionOperator
     *
     * @return array
     */
    public function transform(ConditionOperator $conditionOperator)
    {
        return [

            'id' => (int) $conditionOperator->id,
            'originalName' => $conditionOperator->originalName,
            'apiName' => $conditionOperator->apiName,
        ];
    }
}
