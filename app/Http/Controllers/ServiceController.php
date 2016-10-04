<?php

namespace WA\Http\Controllers;

use Illuminate\Http\Request;

use WA\DataStore\Service\Service;
use WA\DataStore\Service\ServiceTransformer;
use WA\Repositories\Service\ServiceInterface;

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
        $criteria = $this->getRequestCriteria();
        $this->service->setCriteria($criteria);
        $service = $this->service->byPage();
      
        $response = $this->response()->withPaginator($service, new ServiceTransformer(),['key' => 'services']);
        $response = $this->applyMeta($response);
        return $response;        
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
        $service = Service::find($id);
        if($service == null){
            $error['errors']['get'] = 'the Service selected doesn\'t exists';   
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }

        return $this->response()->item($service, new ServiceTransformer(),['key' => 'services']);
    }

    /**
     * Update contents of a Service
     *
     * @param $id
     * @return \Dingo\Api\Http\Response
     */
    public function store($id, Request $request)   
    {
        /*
         * Checks if Json has data, data-type & data-attributes.
         */
        if(!$this->isJsonCorrect($request, 'services')){
            $error['errors']['json'] = 'Json is Invalid';
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        } else {
            $data = $request->all()['data'];
            $dataAttributes = $data['attributes'];           
        }

        $dataAttributes['id'] = $id;
        $service = $this->service->update($dataAttributes);
        return $this->response()->item($service, new ServiceTransformer(), ['key' => 'services']);
    }

    /**
     * Create a new Service
     *
     * @return \Dingo\Api\Http\Response
     */
    public function create(Request $request)
    {
        if(!$this->isJsonCorrect($request, 'services')){
            $error['errors']['json'] = 'Json is Invalid';
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        } else {
            $data = $request->all()['data'];
            $dataAttributes = $data['attributes'];           
        }

        $service = $this->service->create($dataAttributes);
        return $this->response()->item($service, new ServiceTransformer(), ['key' => 'services']);
    }

    /**
     * Delete a Service
     *
     * @param $id
     */
    public function delete($id)
    {
        $service = Service::find($id);
        if($service <> null){
            $this->service->deleteById($id);
        } else {
            $error['errors']['delete'] = 'the service selected doesn\'t exists';   
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }
        
        $this->index();
        $service = Service::find($id);        
        if($service == null){
            return array("success" => true);
        } else {
            $error['errors']['delete'] = 'the service has not been deleted';   
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }
    }
}