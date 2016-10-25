<?php

namespace WA\Http\Controllers;

use Illuminate\Http\Request;

use WA\DataStore\Carrier\Carrier;
use WA\DataStore\Carrier\CarrierTransformer;
use WA\Repositories\Carrier\CarrierInterface;

use DB;

use Illuminate\Support\Facades\Lang;

/**
 * Carrier resource.
 *
 * @Resource("carrier", uri="/carriers")
 */
class CarriersController extends ApiController
{
    /**
     * @var CarrierInterface
     */
    protected $carrier;

    /**
     * Carrier Controller constructor
     *
     * @param CarrierInterface $Carrier
     */
    public function __construct(CarrierInterface $carrier)
    {
        $this->carrier = $carrier;
    }

    /**
     * Show all Carrier
     *
     * Get a payload of all Carrier
     *
     */
    public function index(Request $request)
    {
        $criteria = $this->getRequestCriteria();
        $this->carrier->setCriteria($criteria);
        $carrier = $this->carrier->byPage();

        if(!$this->includesAreCorrect($request, new CarrierTransformer())){
            $error['errors']['getincludes'] = Lang::get('messages.NotExistInclude');
            return response()->json($error)->setStatusCode($this->status_codes['badrequest']);
        }
      
        $response = $this->response()->withPaginator($carrier, new CarrierTransformer(),['key' => 'carriers']);
        $response = $this->applyMeta($response);
        return $response;
    }

    /**
     * Show a single Carrier
     *
     * Get a payload of a single Carrier
     *
     * @Get("/{id}")
     */
    public function show($id, Request $request)
    {
        $criteria = $this->getRequestCriteria();
        $this->carrier->setCriteria($criteria);
        $carrier = $this->carrier->byId($id);

        if($carrier == null){
            $error['errors']['get'] = Lang::get('messages.NotExistClass', ['class' => 'Carrier']);   
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }

        if(!$this->includesAreCorrect($request, new CarrierTransformer())){
            $error['errors']['getincludes'] = Lang::get('messages.NotExistInclude');
            return response()->json($error)->setStatusCode($this->status_codes['badrequest']);
        }

        return $this->response()->item($carrier, new CarrierTransformer(), ['key' => 'carriers'])->setStatusCode($this->status_codes['created']);
    }

    /**
     * Update contents of a Carrier
     *
     * @param $id
     * @return \Dingo\Api\Http\Response
     */
    public function store($id, Request $request)
    {
        /*
         * Checks if Json has data, data-type & data-attributes.
         */
        if(!$this->isJsonCorrect($request, 'carriers')){
            $error['errors']['json'] = Lang::get('messages.InvalidJson');
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }

        DB::beginTransaction();

        try {
            $data = $request->all()['data']['attributes'];
            $data['id'] = $id;
            $carrier = $this->carrier->update($data);

            if($carrier == 'notExist') {
                DB::rollBack();
                $error['errors']['carrier'] = Lang::get('messages.NotExistClass', ['class' => 'Carrier']);
                //$error['errors']['Message'] = $e->getMessage();
                return response()->json($error)->setStatusCode($this->status_codes['notexists']);
            }

            if($carrier == 'notSaved') {
                DB::rollBack();
                $error['errors']['carrier'] = Lang::get('messages.NotSavedClass', ['class' => 'Carrier']);
                //$error['errors']['Message'] = $e->getMessage();
                return response()->json($error)->setStatusCode($this->status_codes['conflict']);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            $error['errors']['carriers'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'Carrier', 'option' => 'updated', 'include' => '']);
            //$error['errors']['carriersMessage'] = $e->getMessage();
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        } 

        if(isset($data['relationships'])){
            if(isset($data['relationships']['images'])){ 
                if(isset($data['relationships']['images']['data'])){
                    try {
                        $dataImages = $this->parseJsonToArray($data['relationships']['images']['data'], 'images');
                        $carrier->images()->sync($dataImages);    
                    } catch (\Exception $e){
                        DB::rollBack();
                        $error['errors']['images'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'Carrier', 'option' => 'created', 'include' => 'Images']);
                        //$error['errors']['Message'] = $e->getMessage();
                        return response()->json($error)->setStatusCode($this->status_codes['conflict']);
                    }
                }
            }
        }

        DB::commit();
        return $this->response()->item($carrier, new CarrierTransformer(), ['key' => 'carriers'])->setStatusCode($this->status_codes['created']);
    }

    /**
     * Create a new Carrier
     *
     * @return \Dingo\Api\Http\Response
     */
    public function create(Request $request)
    {
        /*
         * Checks if Json has data, data-type & data-attributes.
         */
        if(!$this->isJsonCorrect($request, 'carriers')){
            $error['errors']['json'] = Lang::get('messages.InvalidJson');
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }

        DB::beginTransaction();

        try {
            $data = $request->all()['data']['attributes'];
            $carrier = $this->carrier->create($data);
        } catch (\Exception $e) {
            DB::rollBack();
            $error['errors']['carriers'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'Carrier', 'option' => 'created', 'include' => '']);
            //$error['errors']['Message'] = $e->getMessage();
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }        

        if(isset($data['relationships'])){
            if(isset($data['relationships']['images'])){ 
                if(isset($data['relationships']['images']['data'])){
                    try {
                        $dataImages = $this->parseJsonToArray($data['relationships']['images']['data'], 'images');
                        $carrier->images()->sync($dataImages);    
                    } catch (\Exception $e){
                        DB::rollBack();
                        $error['errors']['images'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'Carrier', 'option' => 'created', 'include' => 'Images']);
                        //$error['errors']['Message'] = $e->getMessage();
                        return response()->json($error)->setStatusCode($this->status_codes['conflict']);
                    }
                }
            }
        }

        DB::commit();
        return $this->response()->item($carrier, new CarrierTransformer(), ['key' => 'carriers'])->setStatusCode($this->status_codes['created']);
    }

    /**
     * Delete a Carrier
     *
     * @param $id
     */
    public function delete($id)
    {
        $carrier = Carrier::find($id);
        if($carrier <> null){
            $this->carrier->deleteById($id);
        } else {
            $error['errors']['delete'] = Lang::get('messages.NotExistClass', ['class' => 'Carrier']);   
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }

        $carrier = Carrier::find($id);        
        if($carrier == null){
            return array("success" => true);
        } else {
            $error['errors']['delete'] = Lang::get('messages.NotDeletedClass', ['class' => 'Carrier']);   
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }
    }
}