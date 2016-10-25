<?php

namespace WA\Http\Controllers;

use Illuminate\Http\Request;

use WA\DataStore\Package\Package;
use WA\DataStore\Package\PackageTransformer;
use WA\Repositories\Package\PackageInterface;

use DB;

use Illuminate\Support\Facades\Lang;

/**
 * Package resource.
 *
 * @Resource("Package", uri="/Package")
 */
class PackagesController extends ApiController
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
    public function index(Request $request)
    {
        $criteria = $this->getRequestCriteria();
        $this->package->setCriteria($criteria);
        $package = $this->package->byPage();

        if(!$this->includesAreCorrect($request, new PackageTransformer())){
            $error['errors']['getincludes'] = Lang::get('messages.NotExistInclude');
            return response()->json($error)->setStatusCode($this->status_codes['badrequest']);
        }

        $response = $this->response()->withPaginator($package, new PackageTransformer(), ['key' => 'packages']);
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
        $criteria = $this->getRequestCriteria();
        $this->package->setCriteria($criteria);

        $package = Package::find($id);
        if($package == null){
            $error['errors']['get'] = Lang::get('messages.NotExistClass', ['class' => 'Package']);   
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }

        $packTransformer = new PackageTransformer($criteria);

        if (!$this->includesAreCorrect($request, $packTransformer)) {
            $error['errors']['getIncludes'] = Lang::get('messages.NotExistInclude');
            return response()->json($error)->setStatusCode($this->status_codes['badrequest']);
        }

        $response = $this->response()->item($package, $packTransformer, ['key' => 'packages'])->setStatusCode($this->status_codes['created']);
        $response = $this->applyMeta($response);
        return $response;
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
            $error['errors']['json'] = Lang::get('messages.InvalidJson');
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }

        DB::beginTransaction();

        /*
         * Now we can create the Package.
         */
        try {
            $data = $request->all()['data']['attributes'];
            $data['id'] = $id;
            $package = $this->package->update($data);

            if($package == 'notExist') {
                DB::rollBack();
                $error['errors']['package'] = Lang::get('messages.NotExistClass', ['class' => 'Package']);
                //$error['errors']['Message'] = $e->getMessage();
                return response()->json($error)->setStatusCode($this->status_codes['notexists']);
            }

            if($package == 'notSaved') {
                DB::rollBack();
                $error['errors']['package'] = Lang::get('messages.NotSavedClass', ['class' => 'Package']);
                //$error['errors']['Message'] = $e->getMessage();
                return response()->json($error)->setStatusCode($this->status_codes['conflict']);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            $success = false;
            $error['errors']['packages'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'Package', 'option' => 'updated', 'include' => '']);;
            //$error['errors']['Message'] = $e->getMessage();
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }

        /*
         * Check if Json has relationships to continue or if not and commit + return.
         */
        if (isset($data['relationships'])) {

            $dataRelationships = $data['relationships'];

            if (isset($dataRelationships['conditions'])) {
                if (isset($dataRelationships['conditions']['data'])) {
                    $dataConditions = $this->parseJsonToArray($dataRelationships['conditions']['data'], 'conditions');
                    try {
                        $package->conditions()->sync($dataConditions);    
                    } catch (\Exception $e){
                        $error['errors']['conditions'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'Package', 'option' => 'updated', 'include' => 'Conditions']);
                        //$error['errors']['conditionsMessage'] = $e->getMessage();
                    }
                }
            }

            if (isset($dataRelationships['services'])) {
                if (isset($dataRelationships['services']['data'])) {
                    $dataServices = $this->parseJsonToArray($dataRelationships['services']['data'], 'services');
                    try {
                        $package->services()->sync($dataServices);
                    } catch (\Exception $e){
                        $error['errors']['services'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'Package', 'option' => 'updated', 'include' => 'Services']);
                        //$error['errors']['Message'] = $e->getMessage();
                    }
                }
            }

            if (isset($dataRelationships['devices'])) {
                if (isset($dataRelationships['devices']['data'])) {
                    $dataDevices = $this->parseJsonToArray($dataRelationships['devices']['data'], 'devices');
                    try {
                        $package->devices()->sync($dataDevices);
                    } catch (\Exception $e) {
                        $success = false;
                        $error['errors']['devices'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'Package', 'option' => 'updated', 'include' => 'Devices']);
                        //$error['errors']['Message'] = $e->getMessage();
                    }
                }
            }

            if (isset($dataRelationships['apps'])) {
                if (isset($dataRelationships['apps']['data'])) {
                    $dataApps = $this->parseJsonToArray($dataRelationships['apps']['data'], 'apps');
                    try {
                        $package->apps()->sync($dataApps);
                    } catch (\Exception $e) {
                        $success = false;
                        $error['errors']['apps'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'Package', 'option' => 'updated', 'include' => 'Apps']);;
                        //$error['errors']['Message'] = $e->getMessage();
                    }
                }
            }
        }

        if($success){
            DB::commit();
            return $this->response()->item($package, new PackageTransformer(), ['key' => 'packages'])->setStatusCode($this->status_codes['created']);
        } else {
            DB::rollBack();
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
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
            $error['errors']['json'] = Lang::get('messages.InvalidJson');
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }

        $data = $request->all()['data']['attributes'];

        DB::beginTransaction();

        /*
         * Now we can create the Package.
         */
        try {
            $package = $this->package->create($data);
        } catch (\Exception $e) {
            DB::rollBack();
            $error['errors']['packages'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'Package', 'option' => 'created', 'include' => '']);
            //$error['errors']['Message'] = $e->getMessage();
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }

        /*
         * Check if Json has relationships to continue or if not and commit + return.
         */
        if (isset($data['relationships'])) {

            $dataRelationships = $data['relationships'];

            if (isset($dataRelationships['conditions'])) {
                if (isset($dataRelationships['conditions']['data'])) {
                    $dataConditions = $this->parseJsonToArray($dataRelationships['conditions']['data'], 'conditions');
                    try {
                        $package->conditions()->sync($dataConditions);    
                    } catch (\Exception $e){
                        $success = false;
                        $error['errors']['conditions'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'Package', 'option' => 'created', 'include' => 'Conditions']);
                        //$error['errors']['Message'] = $e->getMessage();
                    }
                }
            }

            if (isset($dataRelationships['services'])) {
                if (isset($dataRelationships['services']['data'])) {
                    $dataServices = $this->parseJsonToArray($dataRelationships['services']['data'], 'services');
                    try {
                        $package->services()->sync($dataServices);
                    } catch (\Exception $e){
                        $success = false;
                        $error['errors']['services'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'Package', 'option' => 'created', 'include' => 'Services']);
                        //$error['errors']['Message'] = $e->getMessage();
                    }
                }
            }

            if (isset($dataRelationships['devices'])) {
                if (isset($dataRelationships['devices']['data'])) {
                    $dataDevices = $this->parseJsonToArray($dataRelationships['devices']['data'], 'devices');
                    try {
                        $package->devices()->sync($dataDevices);
                    } catch (\Exception $e) {
                        $success = false;
                        $error['errors']['devices'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'Package', 'option' => 'created', 'include' => 'Devices']);
                        //$error['errors']['Message'] = $e->getMessage();
                    }
                }
            }

            if (isset($dataRelationships['apps'])) {
                if (isset($dataRelationships['apps']['data'])) {
                    $dataApps = $this->parseJsonToArray($dataRelationships['apps']['data'], 'apps');
                    try {
                        $package->apps()->sync($dataApps);
                    } catch (\Exception $e) {
                        $success = false;
                        $error['errors']['apps'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'Package', 'option' => 'created', 'include' => 'Apps']);
                        //$error['errors']['Message'] = $e->getMessage();
                    }
                }
            }
        }

        if($success){
            DB::commit();
            return $this->response()->item($package, new PackageTransformer(), ['key' => 'packages'])->setStatusCode($this->status_codes['created']);
        } else {
            DB::rollBack();
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
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
        if ($package <> null) {
            $this->package->deleteById($id);
        } else {
            $error['errors']['delete'] = Lang::get('messages.NotExistClass', ['class' => 'Package']);   
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }
        
        $package = Package::find($id);        
        if($package == null){
            return array("success" => true);
        } else {
            $error['errors']['delete'] = Lang::get('messages.NotDeletedClass', ['class' => 'Package']);   
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }
    }
}