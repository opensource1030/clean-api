<?php

namespace WA\Http\Controllers;


use Illuminate\Http\Request;

use WA\DataStore\Price\Price;
use WA\DataStore\Price\PriceTransformer;
use WA\Repositories\Price\PriceInterface;

use WA\Http\Requests\Parameters\Filters;
use Collection;

use Illuminate\Support\Facades\Lang;

/**
 * Price resource.
 *
 * @Resource("Price", uri="/Price")
 */
class PriceController extends ApiController
{
    /**
     * @var PriceInterface
     */
    protected $price;

    /**
     * Price Controller constructor
     *
     * @param PriceInterface $Price
     */
    public function __construct(PriceInterface $price)
    {
        $this->price = $price;
    }

    /**
     * Show all Price
     *
     * Get a payload of all Price
     *
     */
    public function index()
    {
        $criteria = $this->getRequestCriteria();
        $this->price->setCriteria($criteria);
        $price = $this->price->byPage();

        $response = $this->response()->withPaginator($price, new PriceTransformer(), ['key' => 'prices']);
        $response = $this->applyMeta($response);
        return $response;
    }

    /**
     * Show a single Price
     *
     * Get a payload of a single Price
     *
     * @Get("/{id}")
     */
    public function show($id)
    {
        $criteria = $this->getRequestCriteria();
        $this->price->setCriteria($criteria);
        $price = Price::find($id);

        if($price == null){
            $error['errors']['get'] = Lang::get('messages.NotExistClass', ['class' => 'Price']);   
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }

        return $this->response()->item($price, new PriceTransformer(), ['key' => 'prices'])->setStatusCode($this->status_codes['created']);
    }

    /**
     * Update contents of a Price
     *
     * @param $id
     * @return \Dingo\Api\Http\Response
     */
    public function store($id, Request $request)
    {
        /*
         * Checks if Json has data, data-type & data-attributes.
         */
        if(!$this->isJsonCorrect($request, 'prices')){
            $error['errors']['json'] = Lang::get('messages.InvalidJson');
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }

        $data = $request->all()['data']['attributes'];           
        $data['id'] = $id;
        $price = $this->price->update($data);

        if($price == 'notExist') {
            $error['errors']['price'] = Lang::get('messages.NotExistClass', ['class' => 'Price']);
            //$error['errors']['Message'] = $e->getMessage();
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }

        if($price == 'notSaved') {
            $error['errors']['price'] = Lang::get('messages.NotSavedClass', ['class' => 'Price']);
            //$error['errors']['Message'] = $e->getMessage();
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }
        
        return $this->response()->item($price, new PriceTransformer(), ['key' => 'prices'])->setStatusCode($this->status_codes['created']);
    }

    /**
     * Create a new Price
     *
     * @return \Dingo\Api\Http\Response
     */
    public function create(Request $request) {
        
        if(!$this->isJsonCorrect($request, 'prices')){
            $error['errors']['json'] = Lang::get('messages.InvalidJson');
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }
        
        $data = $request->all()['data']['attributes'];
        $price = $this->price->create($data);

        return $this->response()->item($price, new PriceTransformer(), ['key' => 'prices'])->setStatusCode($this->status_codes['created']);
    }

    /**
     * Delete a Price
     *
     * @param $id
     */
    public function delete($id)
    {
        $price = Price::find($id);
        if ($price <> null) {
            $this->price->deleteById($id);
        } else {
            $error['errors']['delete'] = Lang::get('messages.NotExistClass', ['class' => 'Price']);   
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }
        
        
        $price = Price::find($id);        
        if($price == null){
            return array("success" => true);
        } else {
            $error['errors']['delete'] = Lang::get('messages.NotDeletedClass', ['class' => 'Price']);   
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }
    }
}