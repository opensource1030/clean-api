<?php

namespace WA\DataStore\Modification;

use League\Fractal\Resource\Collection as ResourceCollection;
use League\Fractal\Resource\Item as ResourceItem;
use League\Fractal\TransformerAbstract;

/**
 * Class OrderTransformer
 *
 */
class ModificationTransformer extends TransformerAbstract
{
    /**
     * @param Modification $modification
     *
     * @return array
     */
    public function transform(Modification $modification)
    {
        return [
            'id' => (int)$modification->id,
            'modType' => $modification->modType,
            'value' => $modification->value,
            'created_at' => $modification->created_at,
            'updated_at' => $modification->updated_at,
        ];
    }
}