<?php

namespace WA\Http\Controllers;

use Illuminate\Http\Request;

use WA\DataStore\Category\CategoryApp;
use WA\DataStore\Category\CategoryAppTransformer;
use WA\Repositories\Category\CategoryAppsInterface;

use DB;

use Illuminate\Support\Facades\Lang;

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
    public function index(Request $request)
    {
        $criteria = $this->getRequestCriteria();
        $this->categoryApps->setCriteria($criteria);
        $categoryApps = $this->categoryApps->byPage();

        if(!$this->includesAreCorrect($request, new CategoryAppTransformer())){
            $error['errors']['getincludes'] = Lang::get('messages.NotExistInclude');
            return response()->json($error)->setStatusCode($this->status_codes['badrequest']);
        }
      
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
    public function show($id, Request $request)
    {
        $criteria = $this->getRequestCriteria();
        $this->categoryApps->setCriteria($criteria);
        $categoryApps = $this->categoryApps->byId($id);

        if($categoryApps == null){
            $error['errors']['get'] = Lang::get('messages.NotExistClass', ['class' => 'CategoryApps']);   
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }

        if(!$this->includesAreCorrect($request, new CategoryAppTransformer())){
            $error['errors']['getincludes'] = Lang::get('messages.NotExistInclude');   
            return response()->json($error)->setStatusCode($this->status_codes['badrequest']);
        }

        return $this->response()->item($categoryApps, new CategoryAppTransformer(), ['key' => 'categoryapps'])->setStatusCode($this->status_codes['created']);
    }

    /**
     * Update contents of a CategoryApps
     *
     * @param $id
     * @return \Dingo\Api\Http\Response
     */
    public function store($id, Request $request)
    {
        /*
         * Checks if Json has data, data-type & data-attributes.
         */
        if(!$this->isJsonCorrect($request, 'categoryapps')){
            $error['errors']['json'] = Lang::get('messages.InvalidJson');
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }

        DB::beginTransaction();

        try {
            $data = $request->all()['data']['attributes'];
            $data['id'] = $id;
            $categoryApps = $this->categoryApps->update($data);

            if($categoryApps == 'notExist') {
                DB::rollBack();
                $error['errors']['categoryApps'] = Lang::get('messages.NotExistClass', ['class' => 'CategoryApps']);
                //$error['errors']['Message'] = $e->getMessage();
                return response()->json($error)->setStatusCode($this->status_codes['notexists']);
            }

            if($categoryApps == 'notSaved') {
                DB::rollBack();
                $error['errors']['categoryApps'] = Lang::get('messages.NotSavedClass', ['class' => 'CategoryApps']);
                //$error['errors']['Message'] = $e->getMessage();
                return response()->json($error)->setStatusCode($this->status_codes['conflict']);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            $error['errors']['categoryapps'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'CategoryApps', 'option' => 'updated', 'include' => '']);
            //$error['errors']['Message'] = $e->getMessage();
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
                        $error['errors']['images'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'CategoryApps', 'option' => 'created', 'include' => 'Images']);
                        //$error['errors']['Message'] = $e->getMessage();
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
                        $error['errors']['Apps'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'CategoryApps', 'option' => 'created', 'include' => 'Apps']);
                        //$error['errors']['Message'] = $e->getMessage();
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
    public function create(Request $request)
    {
        /*
         * Checks if Json has data, data-type & data-attributes.
         */
        if(!$this->isJsonCorrect($request, 'categoryapps')){
            $error['errors']['json'] = Lang::get('messages.InvalidJson');
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }

        DB::beginTransaction();

        try {
            $data = $request->all()['data']['attributes'];
            $categoryApps = $this->categoryApps->create($data);
        } catch (\Exception $e) {
            DB::rollBack();
            $error['errors']['categoryapps'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'CategoryApps', 'option' => 'created', 'include' => '']);
            //$error['errors']['Message'] = $e->getMessage();
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
                        $error['errors']['images'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'CategoryApps', 'option' => 'created', 'include' => 'Images']);
                        //$error['errors']['Message'] = $e->getMessage();
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
                        $error['errors']['apps'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'CategoryApps', 'option' => 'created', 'include' => 'Apps']);
                        //$error['errors']['Message'] = $e->getMessage();
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
    public function delete($id)
    {
        $categoryApps = CategoryApp::find($id);
        if($categoryApps <> null){
            $this->categoryApps->deleteById($id);
        } else {
            $error['errors']['delete'] = Lang::get('messages.NotExistClass', ['class' => 'CategoryApps']);   
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }
        
        $categoryApps = CategoryApp::find($id);        
        if($categoryApps == null){
            return array("success" => true);
        } else {
            $error['errors']['delete'] = Lang::get('messages.NotDeletedClass', ['class' => 'CategoryApps']);   
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }
    }
}