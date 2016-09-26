<?php
namespace WA\Http\Controllers;

use Illuminate\Http\Request;
use WA\DataStore\Category\CategoryApps;
use WA\DataStore\Category\CategoryAppsTransformer;
use WA\Repositories\Category\CategoryAppsInterface;

use DB;
/**
 * CategoryApps resource.
 *
 * @Resource("categoryApps", uri="/categoryApps")
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
      
        $response = $this->response()->withPaginator($categoryApps, new CategoryAppsTransformer(),['key' => 'CategoryApps']);
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
    public function show($id)
    {
        $categoryApps = CategoryApps::find($id);
        if($categoryApps == null){
            $error['errors']['get'] = 'the CategoryApps selected doesn\'t exists';   
            return response()->json($error)->setStatusCode(409);
        }

        return $this->response()->item($categoryApps, new CategoryAppsTransformer(), ['key' => 'CategoryApps']);
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
        if(!$this->isJsonCorrect($request, 'CategoryApps')){
            $error['errors']['json'] = 'Json is Invalid';
            return response()->json($error)->setStatusCode(409);
        } else {
            $data = $request->all()['data'];
            $dataAttributes = $data['attributes'];           
        }

        try {
            $dataAttributes['id'] = $id;
            $categoryApps = $this->categoryApps->update($dataAttributes);
        } catch (\Exception $e) {
            $success = false;
            $error['errors']['CategoryApps'] = 'The CategoryApps can not be updated';
            //$error['errors']['CategoryAppsMessage'] = $e->getMessage();
            return response()->json($error)->setStatusCode($this->errors['accepted']);
        } 

        if(isset($data['relationships'])){
            if(isset($data['relationships']['images'])){ 
                if(isset($data['relationships']['images']['data'])){
                    $dataImages = $this->parseJsonToArray($data['relationships']['images']['data'], 'images');
                    try {
                        $categoryApps->images()->sync($dataImages);    
                    } catch (\Exception $e){
                        $error['errors']['images'] = 'the CategoryApps Images can not be created';
                        //$error['errors']['imagesMessage'] = $e->getMessage();
                    }
                }
            }

            if(isset($data['relationships']['Apps'])){ 
                if(isset($data['relationships']['Apps']['data'])){
                    $dataApps = $this->parseJsonToArray($data['relationships']['Apps']['data'], 'Apps');
                    try {
                        $categoryApps->Apps()->sync($dataApps);    
                    } catch (\Exception $e){
                        $error['errors']['Apps'] = 'the CategoryApps Apps can not be created';
                        //$error['errors']['AppsMessage'] = $e->getMessage();
                    }
                }
            }
        }

        $dataAttributes['id'] = $id;
        $categoryApps = $this->categoryApps->update($dataAttributes);
        return $this->response()->item($categoryApps, new CategoryAppsTransformer(), ['key' => 'CategoryApps']);
    }

    /**
     * Create a new CategoryApps
     *
     * @return \Dingo\Api\Http\Response
     */
    public function create(Request $request)
    {
        if(!$this->isJsonCorrect($request, 'CategoryApps')){
            $error['errors']['json'] = 'Json is Invalid';
            return response()->json($error)->setStatusCode(409);
        } else {
            $data = $request->all()['data'];
            $dataAttributes = $data['attributes'];           
        }

        try {
            $categoryApps = $this->categoryApps->create($dataAttributes);
        } catch (\Exception $e) {
            $success = false;
            $error['errors']['CategoryApps'] = 'The CategoryApps can not be created';
            $error['errors']['CategoryAppsMessage'] = $e->getMessage();
            return response()->json($error)->setStatusCode($this->errors['accepted']);
        }        

        if(isset($data['relationships'])){
            if(isset($data['relationships']['images'])){ 
                if(isset($data['relationships']['images']['data'])){
                    $dataImages = $this->parseJsonToArray($data['relationships']['images']['data'], 'images');
                    try {
                        $categoryApps->images()->sync($dataImages);    
                    } catch (\Exception $e){
                        $error['errors']['images'] = 'the CategoryApps Images can not be created';
                        $error['errors']['imagesMessage'] = $e->getMessage();
                        return response()->json($error)->setStatusCode($this->errors['accepted']);
                    }
                }
            }

            if(isset($data['relationships']['Apps'])){ 
                if(isset($data['relationships']['Apps']['data'])){
                    $dataApps = $this->parseJsonToArray($data['relationships']['Apps']['data'], 'Apps');
                    try {
                        $categoryApps->Apps()->sync($dataApps);    
                    } catch (\Exception $e){
                        $error['errors']['Apps'] = 'the CategoryApps Apps can not be created';
                        //$error['errors']['AppsMessage'] = $e->getMessage();
                    }
                }
            }
        }

        return $this->response()->item($categoryApps, new CategoryAppsTransformer(), ['key' => 'CategoryApps']);
    }

    /**
     * Delete a CategoryApps
     *
     * @param $id
     */
    public function delete($id)
    {
        $categoryApps = CategoryApps::find($id);
        if($categoryApps <> null){
            $this->categoryApps->deleteById($id);
        } else {
            $error['errors']['delete'] = 'the CategoryApps selected doesn\'t exists';   
            return response()->json($error)->setStatusCode(409);
        }
        
        $this->index();
        $categoryApps = CategoryApps::find($id);        
        if($categoryApps == null){
            return array("success" => true);
        } else {
            $error['errors']['delete'] = 'the CategoryApps has not been deleted';   
            return response()->json($error)->setStatusCode(409);
        }
    }
}