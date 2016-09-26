<?php

namespace WA\DataStore\Image;

use League\Fractal\Resource\Collection as ResourceCollection;
use League\Fractal\Resource\Item as ResourceItem;
use League\Fractal\TransformerAbstract;

/**
 * Class ImageTransformer
 *
 */
class ImageTransformer extends TransformerAbstract
{
    /**
     * @param Image $image
     *
     * @return array
     */
    public function transform(Image $image)
    {
        return [

            'id' => (int)$image->id,
            'originalName' => $image->originalName,
            'filename' => $image->filename,
            'mimeType' => $image->mimeType,
            'extension' => $image->extension,
            'size' => $image->size,
            'url' => $image->url,
            'created_at' => $image->created_at,
            'updated_at' => $image->updated_at
        ];
    }
}