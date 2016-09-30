<?php

namespace WA\Http\Controllers;

use Illuminate\Http\Request;

use WA\DataStore\Category\CategoryApp;
use WA\DataStore\Category\CategoryAppTransformer;
use WA\Repositories\Category\CategoryAppsInterface;

use DB;

/**
 * CategoryApps resource.
 *
 * @Resource("categoryapps", uri="/categoryapps")
 */
class CategoryAppsController extends ApiController
{
    /**
     * @var CategoryAppsInterface
     */
    protected $categoryApps;

    /**
     * CategoryApps Controller constructor
     *
     * @param CategoryAppsInterface $categoryApps
     */
    public function __construct(CategoryAppsInterface $categoryApps)
    {
        $this->categoryApps = $categoryApps;
    }

    /**
     * Show all CategoryApps
     *
     * Get a payload of all CategoryApps
     *
     */
    public function index() {

        $criteria = $this->getRequestCriteria();
        $this->categoryApps->setCriteria($criteria);
        $categoryApps = $this->categoryApps->byPage();
      
        $response = $this->response()->withPaginator($categoryApps, new CategoryAppTransformer(),['key' => 'categoryapps']);
        $response = $this->applyMeta($response);
        return $response;
    }

    /**
     * Show a single CategoryApps
     *
     * Get a payload of a single CategoryApps
     *
     * @Get("/{id}")
     */
    public function show($id, Request $request) {

        $categoryApps = CategoryApp::find($id);
        if($categoryApps == null){
            $error['errors']['get'] = 'the CategoryApps selected doesn\'t exists';   
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }

        if(!$this->includesAreCorrect($request, new CategoryAppTransformer())){
            $error['errors']['getincludes'] = 'One or More Includes selected doesn\'t exists';   
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);            
        }

        return $this->response()->item($categoryApps, new CategoryAppTransformer(), ['key' => 'categoryapps'])->setStatusCode($this->status_codes['created']);
    }

    /**
     * Update contents of a CategoryApps
     *
     * @param $id
     * @return \Dingo\Api\Http\Response
     */
    public function store($id, Request $request) {

        /*
         * Checks if Json has data, data-type & data-attributes.
         */
        if(!$this->isJsonCorrect($request, 'categoryapps')){
            $error['errors']['json'] = 'Json is Invalid';
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }

        DB::beginTransaction();

        try {
            $data = $request->all()['data']['attributes'];
            $data['id'] = $id;
            $categoryApps = $this->categoryApps->update($data);
        } catch (\Exception $e) {
            DB::rollBack();
            $error['errors']['categoryapps'] = 'The CategoryApps has not been updated';
            //$error['errors']['CategoryAppsMessage'] = $e->getMessage();
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        } 

        if(isset($data['relationships'])){
            if(isset($data['relationships']['images'])){ 
                if(isset($data['relationships']['images']['data'])){
                    try {
                        $dataImages = $this->parseJsonToArray($data['relationships']['images']['data'], 'images');
                        $categoryApps->images()->sync($dataImages);    
                    } catch (\Exception $e){
                        DB::rollBack();
                        $error['errors']['images'] = 'the CategoryApps Images has not been created';
                        //$error['errors']['imagesMessage'] = $e->getMessage();
                        return response()->json($error)->setStatusCode($this->status_codes['conflict']);
                    }
                }
            }

            if(isset($data['relationships']['Apps'])){
                if(isset($data['relationships']['Apps']['data'])){
                    try {
                        $dataApps = $this->parseJsonToArray($data['relationships']['apps']['data'], 'apps');
                        $categoryApps->apps()->sync($dataApps);    
                    } catch (\Exception $e){
                        DB::rollBack();
                        $error['errors']['Apps'] = 'the CategoryApps Apps has not been created';
                        //$error['errors']['AppsMessage'] = $e->getMessage();
                        return response()->json($error)->setStatusCode($this->status_codes['conflict']);
                    }
                }
            }
        }

        DB::commit();
        return $this->response()->item($categoryApps, new CategoryAppTransformer(), ['key' => 'categoryapps'])->setStatusCode($this->status_codes['created']);
    }

    /**
     * Create a new CategoryApps
     *
     * @return \Dingo\Api\Http\Response
     */
    public function create(Request $request) {

        /*
         * Checks if Json has data, data-type & data-attributes.
         */
        if(!$this->isJsonCorrect($request, 'categoryapps')){
            $error['errors']['json'] = 'Json is Invalid';
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }

        DB::beginTransaction();

        try {
            $data = $request->all()['data']['attributes'];
            $categoryApps = $this->categoryApps->create($data);
        } catch (\Exception $e) {
            DB::rollBack();
            $error['errors']['categoryapps'] = 'The CategoryApps has not been created';
            //$error['errors']['CategoryAppsMessage'] = $e->getMessage();
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }        

        if(isset($data['relationships'])){
            if(isset($data['relationships']['images'])){ 
                if(isset($data['relationships']['images']['data'])){
                    try {
                        $dataImages = $this->parseJsonToArray($data['relationships']['images']['data'], 'images');
                        $categoryApps->images()->sync($dataImages);    
                    } catch (\Exception $e){
                        DB::rollBack();
                        $error['errors']['images'] = 'the CategoryApps Images has not been created';
                        //$error['errors']['imagesMessage'] = $e->getMessage();
                        return response()->json($error)->setStatusCode($this->status_codes['conflict']);
                    }
                }
            }

            if(isset($data['relationships']['Apps'])){ 
                if(isset($data['relationships']['Apps']['data'])){
                    try {
                        $dataApps = $this->parseJsonToArray($data['relationships']['apps']['data'], 'apps');
                        $categoryApps->Apps()->sync($dataApps);    
                    } catch (\Exception $e){
                        DB::rollBack();
                        $error['errors']['apps'] = 'the CategoryApps Apps has not been created';
                        //$error['errors']['AppsMessage'] = $e->getMessage();
                        return response()->json($error)->setStatusCode($this->status_codes['conflict']);
                    }
                }
            }
        }

        DB::commit();
        return $this->response()->item($categoryApps, new CategoryAppTransformer(), ['key' => 'categoryapps'])->setStatusCode($this->status_codes['created']);
    }

    /**
     * Delete a CategoryApps
     *
     * @param $id
     */
    public function delete($id) {

        $categoryApps = CategoryApp::find($id);
        if($categoryApps <> null){
            $this->categoryApps->deleteById($id);
        } else {
            $error['errors']['delete'] = 'the CategoryApps selected doesn\'t exists';   
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }
        
        $this->index();
        $categoryApps = CategoryApp::find($id);        
        if($categoryApps == null){
            return array("success" => true);
        } else {
            $error['errors']['delete'] = 'the CategoryApps has not been deleted';   
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }
    }
}