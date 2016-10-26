<?php

namespace WA\DataStore\Condition;

use League\Fractal\TransformerAbstract;

/**
 * Class ConditionTransformer.
 */
class ConditionFieldTransformer extends TransformerAbstract
{
    /**
     * @param ConditionField $conditionField
     *
     * @return array
     */
    public function transform(ConditionField $conditionField)
    {
        return [

            'id' => (int) $conditionField->id,
            'typeField' => $conditionField->typeField,
            'field' => $conditionField->field,
        ];
    }
}
