<?php

namespace WA\DataStore\Udl;

use WA\DataStore\FilterableTransformer;

/**
 * Class UdlTransformer.
 */
class UdlTransformer extends FilterableTransformer
{
    /**
     * @param Udl $udl
     *
     * @return array
     */
    public function transform(Udl $udl)
    {
        return [
            'id'    => $udl->id,
            'name'  => $udl->name,
            'label' => $udl->label,
            'sections' => $udl->udlValues,
        ];
    }
}
