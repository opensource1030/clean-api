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
    public function __construct(CategoryDevicesInterface $categoryDevices)
    {
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
      
        $response = $this->response()->withPaginator($categoryDevices, new CategoryDevicesTransformer(),['key' => 'CategoryDevicess']);
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
    public function show($id)
    {
        $categoryDevices = CategoryDevices::find($id);
        if($categoryDevices == null){
            $error['errors']['get'] = 'the CategoryDevices selected doesn\'t exists';   
            return response()->json($error)->setStatusCode(409);
        }

        return $this->response()->item($categoryDevices, new CategoryDevicesTransformer(), ['key' => 'CategoryDevicess']);
    }

    /**
     * Update contents of a CategoryDevices
     *
     * @param $id
     * @return \Dingo\Api\Http\Response
     */
    public function store($id, Request $request)   
    {
        /*
         * Checks if Json has data, data-type & data-attributes.
         */
        if(!$this->isJsonCorrect($request, 'CategoryDevicess')){
            $error['errors']['json'] = 'Json is Invalid';
            return response()->json($error)->setStatusCode(409);
        } else {
            $data = $request->all()['data'];
            $dataAttributes = $data['attributes'];           
        }

        try {
            $dataAttributes['id'] = $id;
            $categoryDevices = $this->categoryDevices->update($dataAttributes);
        } catch (\Exception $e) {
            $success = false;
            $error['errors']['CategoryDevicess'] = 'The CategoryDevices can not be updated';
            //$error['errors']['CategoryDevicessMessage'] = $e->getMessage();
            return response()->json($error)->setStatusCode($this->errors['accepted']);
        } 

        if(isset($data['relationships'])){
            if(isset($data['relationships']['images'])){ 
                if(isset($data['relationships']['images']['data'])){
                    $dataImages = $this->parseJsonToArray($data['relationships']['images']['data'], 'images');
                    try {
                        $categoryDevices->images()->sync($dataImages);    
                    } catch (\Exception $e){
                        $error['errors']['images'] = 'the CategoryDevices Images can not be created';
                        //$error['errors']['imagesMessage'] = $e->getMessage();
                    }
                }
            }

            if(isset($data['relationships']['devices'])){ 
                if(isset($data['relationships']['devices']['data'])){
                    $dataDevices = $this->parseJsonToArray($data['relationships']['devices']['data'], 'devices');
                    try {
                        $categoryDevices->devices()->sync($dataDevices);    
                    } catch (\Exception $e){
                        $error['errors']['devices'] = 'the CategoryDevices Devices can not be created';
                        //$error['errors']['devicesMessage'] = $e->getMessage();
                    }
                }
            }
        }

        $dataAttributes['id'] = $id;
        $categoryDevices = $this->categoryDevices->update($dataAttributes);
        return $this->response()->item($categoryDevices, new CategoryDevicesTransformer(), ['key' => 'CategoryDevicess']);
    }

    /**
     * Create a new CategoryDevices
     *
     * @return \Dingo\Api\Http\Response
     */
    public function create(Request $request)
    {
        if(!$this->isJsonCorrect($request, 'CategoryDevicess')){
            $error['errors']['json'] = 'Json is Invalid';
            return response()->json($error)->setStatusCode(409);
        } else {
            $data = $request->all()['data'];
            $dataAttributes = $data['attributes'];           
        }

        try {
            $categoryDevices = $this->categoryDevices->create($dataAttributes);
        } catch (\Exception $e) {
            $success = false;
            $error['errors']['CategoryDevicess'] = 'The CategoryDevices can not be created';
            $error['errors']['CategoryDevicessMessage'] = $e->getMessage();
            return response()->json($error)->setStatusCode($this->errors['accepted']);
        }        

        if(isset($data['relationships'])){
            if(isset($data['relationships']['images'])){ 
                if(isset($data['relationships']['images']['data'])){
                    $dataImages = $this->parseJsonToArray($data['relationships']['images']['data'], 'images');
                    try {
                        $categoryDevices->images()->sync($dataImages);    
                    } catch (\Exception $e){
                        $error['errors']['images'] = 'the CategoryDevices Images can not be created';
                        $error['errors']['imagesMessage'] = $e->getMessage();
                        return response()->json($error)->setStatusCode($this->errors['accepted']);
                    }
                }
            }

            if(isset($data['relationships']['devices'])){ 
                if(isset($data['relationships']['devices']['data'])){
                    $dataDevices = $this->parseJsonToArray($data['relationships']['devices']['data'], 'devices');
                    try {
                        $categoryDevices->devices()->sync($dataDevices);    
                    } catch (\Exception $e){
                        $error['errors']['devices'] = 'the CategoryDevices Devices can not be created';
                        //$error['errors']['devicesMessage'] = $e->getMessage();
                    }
                }
            }
        }

        return $this->response()->item($categoryDevices, new CategoryDevicesTransformer(), ['key' => 'CategoryDevicess']);
    }

    /**
     * Delete a CategoryDevices
     *
     * @param $id
     */
    public function delete($id)
    {
        $categoryDevices = CategoryDevices::find($id);
        if($categoryDevices <> null){
            $this->categoryDevices->deleteById($id);
        } else {
            $error['errors']['delete'] = 'the CategoryDevices selected doesn\'t exists';   
            return response()->json($error)->setStatusCode(409);
        }
        
        $this->index();
        $categoryDevices = CategoryDevices::find($id);        
        if($categoryDevices == null){
            return array("success" => true);
        } else {
            $error['errors']['delete'] = 'the CategoryDevices has not been deleted';   
            return response()->json($error)->setStatusCode(409);
        }
    }
}