<?php

namespace WA\DataStore\Udl;

use League\Fractal\TransformerAbstract;

/**
 * Class UdlTransformer.
 */
class UdlTransformer extends TransformerAbstract
{
    /**
     * @param Udl $udl
     *
     * @return array
     */
    public function transform(Udl $udl)
    {
        return [
            'id' => $udl->id,
            'name' => $udl->name,
            'label' => $udl->label,

            'sections' => $udl->udlValues,
        ];
    }
}
