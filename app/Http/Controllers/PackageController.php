<?php

namespace WA\Http\Controllers;

use Cartalyst\DataGrid\Laravel\Facades\DataGrid;
use Dingo\Api\Routing\Helpers;
use Illuminate\Session\SessionManager as Session;
use WA\DataStore\Package\Package;
use WA\DataStore\Package\PackageTransformer;
use WA\Helpers\Traits\SetLimits;
use WA\Http\Controllers\Api\Traits\BasicCrud;
use WA\Repositories\Package\PackageInterface;
use Illuminate\Http\Request;

use DB;

/**
 * Package resource.
 *
 * @Resource("Package", uri="/Package")
 */
class PackageController extends ApiController
{
    /**
     * @var PackageInterface
     */
    protected $package;

    /**
     * Package Controller constructor
     *
     * @param PackageInterface $Package
     */
    public function __construct(PackageInterface $package)
    {
        $this->package = $package;
    }

    /**
     * Show all Package
     *
     * Get a payload of all Package
     *
     */
    public function index()
    {
        $criteria = $this->getRequestCriteria();
        $this->package->setCriteria($criteria);

        $package = $this->package->byPage();
        
        $response = $this->response()->withPaginator($package, new PackageTransformer(),['key' => 'package']);
        $response = $this->applyMeta($response);
        return $response;
    }

    /**
     * Show a single Package
     *
     * Get a payload of a single Package
     *
     * @Get("/{id}")
     */
    public function show($id)
    {
        $package = Package::find($id);
        if($device == null){
            $error['errors']['get'] = 'the Device selected doesn\'t exists';   
            return response()->json($error)->setStatusCode(409);
        }

        return $this->response()->item($package, new PackageTransformer(), ['key' => 'packages']);
    }

    /**
     * Update contents of a Package
     *
     * @param $id
     * @return \Dingo\Api\Http\Response
     */
    public function store($id, Request $request)   
    {
        $data = $request->all();       
        $data['id'] = $id;
        $package = $this->package->update($data);
        return $this->response()->item($package, new PackageTransformer(), ['key' => 'packages']);
    }

    /**
     * Create a new Package
     *
     * @return \Dingo\Api\Http\Response
     */
    public function create(Request $request)
    {
        /*
        $data = $request->all();
        $package = $this->package->create($data);
        return $this->response()->item($package, new PackageTransformer(), ['key' => 'packages']);
        */

        $success = true;
        $dataConditions = $dataServices = $dataDevices = $dataApps = $dataDelivery = array();

        /*
         * Checks if Json has data, data-type & data-attributes.
         */
        if(!$this->isJsonCorrect($request, 'packages')){
            $error['errors']['json'] = 'Json is Invalid';
            return response()->json($error)->setStatusCode(409);
        } else {
            $data = $request->all()['data'];
            $dataType = $data['type'];
            $dataAttributes = $data['attributes'];           
        }

        DB::beginTransaction();

        /*
         * Now we can create the Package.
         */       
        try{
            $package = $this->package->create($dataAttributes);
        } catch (\Exception $e) {
            DB::rollBack();
            $success = false;
            $error['errors']['packages'] = 'The Package can not be created';
            $error['errors']['packagesMessage'] = $this->getErrorAndParse($e);
            return response()->json($error)->setStatusCode(409);
        }

        /*
         * Check if Json has relationships to continue or if not and commit + return.
         */
        if(isset($data['relationships'])){

            $dataRelationships = $data['relationships'];

            if(isset($dataRelationships['conditions'])){ 
                if(isset($dataRelationships['conditions']['data'])){
                    $dataConditions = $this->parseJsonToArray($dataRelationships['conditions']['data'], 'conditions');
                    try {
                        $package->conditions()->sync($dataConditions);    
                    } catch (\Exception $e){
                        $error['errors']['conditions'] = 'the Package Conditions can not be created';
                        //$error['errors']['imagesMessage'] = $this->getErrorAndParse($e);
                    }
                }
            }

            if(isset($dataRelationships['services'])){ 
                if(isset($dataRelationships['services']['data'])){
                    $dataServices = $this->parseJsonToArray($dataRelationships['services']['data'], 'services');
                    try {
                        $package->services()->sync($dataServices);
                    } catch (\Exception $e){
                        $error['errors']['services'] = 'the Package Services can not be created';
                        //$error['errors']['servicesMessage'] = $this->getErrorAndParse($e);
                    }
                }
            }

            if(isset($dataRelationships['devices'])){ 
                if(isset($dataRelationships['devices']['data'])){
                    $dataDevices = $this->parseJsonToArray($dataRelationships['devices']['data'], 'devices');
                     try {
                        $package->devices()->sync($dataDevices);
                    } catch (\Exception $e){
                        $success = false;
                        $error['errors']['devices'] = 'the Package Devices can not be created';
                        //$error['errors']['devicesMessage'] = $this->getErrorAndParse($e);
                    }
                }
            }

            if(isset($dataRelationships['apps'])){ 
                if(isset($dataRelationships['apps']['data'])){
                    $dataApps = $this->parseJsonToArray($dataRelationships['apps']['data'], 'apps');
                    try {
                        $package->apps()->sync($dataApps);
                    } catch (\Exception $e){
                        $success = false;
                        $error['errors']['apps'] = 'the Package Apps can not be created';
                        //$error['errors']['appsMessage'] = $this->getErrorAndParse($e);
                    }
                }
            }
        }

        if(!$success){
            DB::rollBack();
            return response()->json($error)->setStatusCode(409);
        } else {
            DB::commit();
            return $this->response()->item($package, new DeviceTransformer(), ['key' => 'packages']);
        }
    }

    /**
     * Delete a Package
     *
     * @param $id
     */
    public function delete($id)
    {
        $this->package->deleteById($id);
        $this->index();
    }
}
