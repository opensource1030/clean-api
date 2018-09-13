<?php

namespace WA\Http\Controllers;

use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use WA\DataStore\Location\Location;
use WA\DataStore\Location\LocationTransformer;
use WA\Repositories\Location\LocationInterface;

/**
 * Location resource.
 *
 * @Resource("Location", uri="/locations")
 */
class LocationsController extends FilteredApiController
{
    /**
     * @var LocationInterface
     */
    protected $location;

    /**
     * LocationsController constructor.
     *
     * @param LocationInterface $location
     * @param Request $request
     */
    public function __construct(LocationInterface $location, Request $request)
    {
        parent::__construct($location, $request);
        $this->location = $location;
    }

}
