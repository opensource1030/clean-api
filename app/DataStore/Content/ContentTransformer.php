<?php

namespace WA\DataStore\Content;

use League\Fractal\TransformerAbstract;

/**
 * Class ContentTransformer.
 */
class ContentTransformer extends TransformerAbstract
{
    /**
     * @param Content $content
     *
     * @return array
     */
    public function transform(Content $content)
    {
        return [

            'id' => (int) $content->id,

            'content' => $content->content,

            'active' => $content->active,

            'owner_type' => $content->owner_type,

            'owner_id' => $content->owner_id,
        ];
    }
}
