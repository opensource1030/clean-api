<?php

namespace WA\Http\Controllers;

use Auth;
use Dingo\Api\Routing\Helpers;
use Illuminate\Contracts\Routing\ResponseFactory as Response;
use Input;
use League\Fractal\Manager as FractalManager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection as FractalCollection;
use League\Fractal\Resource\Item as FractalItem;
use League\Fractal\Serializer\ArraySerializer;
use League\Fractal\TransformerAbstract;
use WA\Http\Controllers\BaseController;
use WA\Services\ApiHandler\SQL\ApiHandler as ApiHandler;
use LucaDegasperi\OAuth2Server\Authorizer;


/**
 * Class ApiController.
 */
abstract class ApiController extends BaseController
{
    use Helpers;

    /**
     * Additional meta information.
     *
     * @var array
     */
    protected $meta = [];

    
    /**
     * @param array $meta
     *
     * @return array
     */
    protected function setMetaData(array $meta)
    {
        $this->meta = array_merge($this->meta, $meta);
    }

}
