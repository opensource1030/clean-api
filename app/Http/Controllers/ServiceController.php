<?php

namespace WA\Http\Controllers;

use Cartalyst\DataGrid\Laravel\Facades\DataGrid;
use Dingo\Api\Routing\Helpers;
use Illuminate\Session\SessionManager as Session;
use WA\DataStore\Service\ServiceTransformer;
use WA\Helpers\Traits\SetLimits;
use WA\Http\Controllers\Api\Traits\BasicCrud;
use WA\Repositories\Service\ServiceInterface;

use Illuminate\Http\Request;
/**
 * Service resource.
 *
 * @Resource("Service", uri="/services")
 */
class ServiceController extends ApiController
{
    /**
     * @var ServiceInterface
     */
    protected $service;

    /**
     * Service Controller constructor
     *
     * @param ServiceInterface $Service
     */
    public function __construct(ServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * Show all Service
     *
     * Get a payload of all Service
     *
     * @Get("/")
     * @Parameters({
     *      @Parameter("page", description="The page of results to view.", default=1),
     *      @Parameter("limit", description="The amount of results per page.", default=10),
     *      @Parameter("access_token", required=true, description="Access token for authentication")
     * })
     */
    public function index()
    {
        $service = $this->service->byPage();
      
        return $this->response()->withPaginator($service, new ServiceTransformer(),['key' => 'services']);
        
    }

    /**
     * Show a single Service
     *
     * Get a payload of a single Service
     *
     * @Get("/{id}")
     */
    public function show($id)
    {
        $service = $this->service->byId($id);

        return $this->response()->item($service, new ServiceTransformer(), ['key' => 'services']);
    }

    /**
     * Update contents of a Service
     *
     * @param $id
     * @return \Dingo\Api\Http\Response
     */
    public function store($id, Request $request)   
    {
        $data = $request->all();
        $data['id'] = $id;
        $service = $this->service->update($data);
        return $this->response()->item($service, new ServiceTransformer(), ['key' => 'services']);
    }

    /**
     * Create a new Service
     *
     * @return \Dingo\Api\Http\Response
     */
    public function create(Request $request)
    {
        $data = $request->all();
        $service = $this->service->create($data);
        return $this->response()->item($service, new ServiceTransformer(), ['key' => 'services']);
    }

    /**
     * Delete a Service
     *
     * @param $id
     */
    public function delete($id)
    {
        $this->service->deleteById($id);
        $this->index();
    }
}