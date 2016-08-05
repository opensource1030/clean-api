<?php

namespace WA\DataStore\UdlValuePath;

use League\Fractal\TransformerAbstract;

/**
 * Class UdlValuePathTransformer.
 */
class UdlValuePathTransformer extends TransformerAbstract
{
    /**
     * @param UdlValuePath $udlValuePath
     *
     * @return array
     */
    public function transform(UdlValuePath $udlValuePath)
    {
        return [
            'id' => $udlValuePath->id,
            'name' => $udlValuePath->udlPath,
            'lastUpdated' => $udlValuePath->updated_at,
        ];
    }
}
