<?php

namespace WA\DataStore\Page;

use League\Fractal\Resource\Collection as ResourceCollection;
use League\Fractal\Resource\Item as ResourceItem;
use League\Fractal\TransformerAbstract;

/**
 * Class PageTransformer
 *
 */
class PageTransformer extends TransformerAbstract
{

    /**
     * @param Page $page
     *
     * @return array
     */
    public function transform(Page $page)
    {
        return [

            'id' => (int)$page->id,

            'title' => $page->title,

            'section' => $page->section,

            'content' => $page->content,

            'active' => $page->active,

            'owner_type' => $page->owner_type,

            'owner_id' => $page->owner_id,
        ];
    }
}