<?php

namespace WA\Http\Controllers;

use Illuminate\Http\Request;

use WA\DataStore\Package\Package;
use WA\DataStore\Package\PackageTransformer;
use WA\Repositories\Package\PackageInterface;

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
        
        $response = $this->response()->withPaginator($package, new PackageTransformer(),['key' => 'packages']);
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
    public function show($id, Request $request)
    {
        $package = Package::find($id);
        if($package == null){
            $error['errors']['get'] = 'the Package selected doesn\'t exists';   
            return response()->json($error)->setStatusCode(409);
        }

        if(!$this->includesAreCorrect($request, new PackageTransformer())){
            $error['errors']['getincludes'] = 'One or More Includes selected doesn\'t exists';
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
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
        $success = true;
        $dataConditions = $dataServices = $dataDevices = $dataApps = array();

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
            $package = Package::find($id);

            $package->name = isset($dataAttributes['name']) ? $dataAttributes['name'] : $package->name;
            $package->addressId = isset($dataAttributes['addressId']) ? $dataAttributes['addressId'] : $package->addressId;
            
            $package->save();
        } catch (\Exception $e) {
            DB::rollBack();
            $success = false;
            $error['errors']['packages'] = 'The Package has not been Modified';
            $error['errors']['packagesMessage'] = $e->getMessage();
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
                        $error['errors']['conditions'] = 'the Package Conditions has not been Modified';
                        $error['errors']['conditionsMessage'] = $e->getMessage();
                    }
                }
            }

            if(isset($dataRelationships['services'])){ 
                if(isset($dataRelationships['services']['data'])){
                    $dataServices = $this->parseJsonToArray($dataRelationships['services']['data'], 'services');
                    try {
                        $package->services()->sync($dataServices);
                    } catch (\Exception $e){
                        $error['errors']['services'] = 'the Package Services has not been Modified';
                        //$error['errors']['servicesMessage'] = $e->getMessage();
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
                        $error['errors']['devices'] = 'the Package Devices has not been Modified';
                        //$error['errors']['devicesMessage'] = $e->getMessage();
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
                        $error['errors']['apps'] = 'the Package Apps has not been Modified';
                        //$error['errors']['appsMessage'] = $e->getMessage();
                    }
                }
            }
        }

        if(!$success){
            DB::rollBack();
            return response()->json($error)->setStatusCode(409);
        } else {
            DB::commit();
            return $this->response()->item($package, new PackageTransformer(), ['key' => 'packages']);
        }
    }

    /**
     * Create a new Package
     *
     * @return \Dingo\Api\Http\Response
     */
    public function create(Request $request)
    {
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
            $error['errors']['packages'] = 'The Package has not been created';
            $error['errors']['packagesMessage'] = $e->getMessage();
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
                        $error['errors']['conditions'] = 'the Package Conditions has not been created';
                        //$error['errors']['conditionsMessage'] = $e->getMessage();
                    }
                }
            }

            if(isset($dataRelationships['services'])){ 
                if(isset($dataRelationships['services']['data'])){
                    $dataServices = $this->parseJsonToArray($dataRelationships['services']['data'], 'services');
                    try {
                        $package->services()->sync($dataServices);
                    } catch (\Exception $e){
                        $error['errors']['services'] = 'the Package Services has not been created';
                        //$error['errors']['servicesMessage'] = $e->getMessage();
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
                        $error['errors']['devices'] = 'the Package Devices has not been created';
                        //$error['errors']['devicesMessage'] = $e->getMessage();
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
                        $error['errors']['apps'] = 'the Package Apps has not been created';
                        //$error['errors']['appsMessage'] = $e->getMessage();
                    }
                }
            }
        }

        if(!$success){
            DB::rollBack();
            return response()->json($error)->setStatusCode(409);
        } else {
            DB::commit();
            return $this->response()->item($package, new PackageTransformer(), ['key' => 'packages']);
        }
    }

    /**
     * Delete a Package
     *
     * @param $id
     */
    public function delete($id)
    {
        $package = Package::find($id);
        if($package <> null){
            $this->package->deleteById($id);
        } else {
            $error['errors']['delete'] = 'the Package selected doesn\'t exists';   
            return response()->json($error)->setStatusCode(409);
        }
        
        $this->index();
        $package = Package::find($id);        
        if($package == null){
            return array("success" => true);
        } else {
            $error['errors']['delete'] = 'the Package has not been deleted';   
            return response()->json($error)->setStatusCode(409);
        }
    }
}