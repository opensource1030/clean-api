<?php

namespace WA\Http\Controllers;

use Illuminate\Http\Request;

use WA\DataStore\Category\CategoryDevices;
use WA\DataStore\Category\CategoryDevicesTransformer;
use WA\Repositories\CategoryDevices\CategoryDevicesInterface;

use DB;

/**
 * CategoryDevices resource.
 *
 * @Resource("categoryDevices", uri="/categoryDevices")
 */
class CategoryDevicesController extends ApiController
{
    /**
     * @var CategoryDevicesInterface
     */
    protected $categoryDevices;

    /**
     * CategoryDevices Controller constructor
     *
     * @param CategoryDevicesInterface $categoryDevices
     */
    public function __construct(CategoryDevicesInterface $categoryDevices) {

        $this->categoryDevices = $categoryDevices;
    }

    /**
     * Show all CategoryDevices
     *
     * Get a payload of all CategoryDevices
     *
     */
    public function index() {

        $criteria = $this->getRequestCriteria();
        $this->categoryDevices->setCriteria($criteria);
        $categoryDevices = $this->categoryDevices->byPage();
      
        $response = $this->response()->withPaginator($categoryDevices, new CategoryDevicesTransformer(),['key' => 'categorydevicess']);
        $response = $this->applyMeta($response);
        return $response;
    }

    /**
     * Show a single CategoryDevices
     *
     * Get a payload of a single CategoryDevices
     *
     * @Get("/{id}")
     */
    public function show($id) {

        $categoryDevices = CategoryDevices::find($id);
        if($categoryDevices == null){
            $error['errors']['get'] = 'the CategoryDevices selected doesn\'t exists';   
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }

        // Dingo\Api\src\Http\Response\Factory.php
        // Dingo\Api\src\Http\Transformer\Factory.php

        return $this->response()->item($categoryDevices, new CategoryDevicesTransformer(), ['key' => 'categorydevices'])->setStatusCode($this->status_codes['created']);
    }

    /**
     * Update contents of a CategoryDevices
     *
     * @param $id
     * @return \Dingo\Api\Http\Response
     */
    public function store($id, Request $request) {

        /*
         * Checks if Json has data, data-type & data-attributes.
         */
        if(!$this->isJsonCorrect($request, 'categorydevices')){
            $error['errors']['json'] = 'Json is Invalid';
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }

        DB::beginTransaction();

        try {
            $data = $request->all()['data']['attributes'];
            $data['id'] = $id;
            $categoryDevices = $this->categoryDevices->update($data);
        } catch (\Exception $e) {
            DB::rollBack();
            $error['errors']['categoryDevices'] = 'The CategoryDevices has not been updated';
            //$error['errors']['categoryDevicesMessage'] = $e->getMessage();
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        } 

        if(isset($data['relationships'])){
            if(isset($data['relationships']['images'])){ 
                if(isset($data['relationships']['images']['data'])){
                    try {
                        $dataImages = $this->parseJsonToArray($data['relationships']['images']['data'], 'images');
                        $categoryDevices->images()->sync($dataImages);    
                    } catch (\Exception $e){
                        DB::rollBack();
                        $error['errors']['images'] = 'the CategoryDevices Images has not been created';
                        //$error['errors']['imagesMessage'] = $e->getMessage();
                        return response()->json($error)->setStatusCode($this->status_codes['conflict']);
                    }
                }
            }

            if(isset($data['relationships']['devices'])){ 
                if(isset($data['relationships']['devices']['data'])){
                    try {
                        $dataDevices = $this->parseJsonToArray($data['relationships']['devices']['data'], 'devices');
                        $categoryDevices->devices()->sync($dataDevices);    
                    } catch (\Exception $e){
                        DB::rollBack();
                        $error['errors']['devices'] = 'the CategoryDevices Devices has not been created';
                        //$error['errors']['devicesMessage'] = $e->getMessage();
                        return response()->json($error)->setStatusCode($this->status_codes['conflict']);
                    }
                }
            }
        }

        DB::commit();
        return $this->response()->item($categoryDevices, new CategoryDevicesTransformer(), ['key' => 'categorydevices'])->setStatusCode($this->status_codes['created']);
    }

    /**
     * Create a new CategoryDevices
     *
     * @return \Dingo\Api\Http\Response
     */
    public function create(Request $request) {

        /*
         * Checks if Json has data, data-type & data-attributes.
         */
        if(!$this->isJsonCorrect($request, 'categorydevices')){
            $error['errors']['json'] = 'Json is Invalid';
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }

        DB::beginTransaction();

        try {
            $data = $request->all()['data']['attributes'];           
            $categoryDevices = $this->categoryDevices->create($data);
        } catch (\Exception $e) {
            DB::rollBack();
            $error['errors']['categoryDevices'] = 'The CategoryDevices has not been created';
            //$error['errors']['categoryDevicesMessage'] = $e->getMessage();
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }        

        if(isset($data['relationships'])){
            if(isset($data['relationships']['images'])){ 
                if(isset($data['relationships']['images']['data'])){
                    try {
                        $dataImages = $this->parseJsonToArray($data['relationships']['images']['data'], 'images');
                        $categoryDevices->images()->sync($dataImages);    
                    } catch (\Exception $e){
                        DB::rollBack();
                        $error['errors']['images'] = 'the CategoryDevices Images has not been created';
                        //$error['errors']['imagesMessage'] = $e->getMessage();
                        return response()->json($error)->setStatusCode($this->status_codes['conflict']);
                    }
                }
            }

            if(isset($data['relationships']['devices'])){ 
                if(isset($data['relationships']['devices']['data'])){
                    try {
                        $dataDevices = $this->parseJsonToArray($data['relationships']['devices']['data'], 'devices');
                        $categoryDevices->devices()->sync($dataDevices);    
                    } catch (\Exception $e){
                        DB::rollBack();
                        $error['errors']['devices'] = 'the CategoryDevices Devices has not been created';
                        //$error['errors']['devicesMessage'] = $e->getMessage();
                        return response()->json($error)->setStatusCode($this->status_codes['conflict']);
                    }
                }
            }
        }

        DB::commit();
        return $this->response()->item($categoryDevices, new CategoryDevicesTransformer(), ['key' => 'CategoryDevicess'])->setStatusCode($this->status_codes['created']);
    }

    /**
     * Delete a CategoryDevices
     *
     * @param $id
     */
    public function delete($id) {

        $categoryDevices = CategoryDevices::find($id);
        if($categoryDevices <> null){
            $this->categoryDevices->deleteById($id);
        } else {
            $error['errors']['delete'] = 'the CategoryDevices selected doesn\'t exists';   
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }
        
        $this->index();
        $categoryDevices = CategoryDevices::find($id);        
        if($categoryDevices == null){
            return array("success" => true);
        } else {
            $error['errors']['delete'] = 'the CategoryDevices has not been deleted';   
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }
    }
}