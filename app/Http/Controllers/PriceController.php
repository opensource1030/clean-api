<?php
namespace WA\Http\Controllers;

use Illuminate\Pagination\LengthAwarePaginator;

use WA\DataStore\Price\Price;
use WA\DataStore\Price\PriceTransformer;
use WA\Repositories\Price\PriceInterface;
use Illuminate\Http\Request;
use WA\Http\Requests\Parameters\Filters;

use Log;
use Collection;

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
    public function index() {

        $criteria = $this->getRequestCriteria();
        $this->price->setCriteria($criteria);
        $price = $this->price->byPage();
      
        $response = $this->response()->withPaginator($price, new PriceTransformer(),['key' => 'prices']);
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
    public function show($id) {

        $price = Price::find($id);
        if($price == null){
            $error['errors']['get'] = 'the price selected doesn\'t exists';   
            return response()->json($error)->setStatusCode(409);
        }

        return $this->response()->item($price, new PriceTransformer(), ['key' => 'prices']);
    }

    /**
     * Show a single Price
     *
     * Get a payload of a single Price
     *
     * @Get("/{id}")
     */
    public function showDevice($id) {

        $filter = new Filters([
            "deviceId" => ["eq" => $id]
            ]);
        
        $this->price->setFilters($filter);

        $prices = $this->price->byPage();
        
        $response = $this->response()->withPaginator($prices, new PriceTransformer(), ['key' => 'prices']);
    }

    /**
     * Show a single Price
     *
     * Get a payload of a single Price
     *
     * @Get("/{id}")
     */
    public function showCapacity($id) {

        $filter = new Filters([
            "capacityId" => ["eq" => $id]
            ]);
        
        $this->price->setFilters($filter);

        $prices = $this->price->byPage();
        
        $response = $this->response()->withPaginator($prices, new PriceTransformer(), ['key' => 'prices']);
    }

    /**
     * Show a single Price
     *
     * Get a payload of a single Price
     *
     * @Get("/{id}")
     */
    public function showStyle($id) {

        $filter = new Filters([
            "styleId" => ["eq" => $id]
            ]);
        
        $this->price->setFilters($filter);

        $prices = $this->price->byPage();
        
        $response = $this->response()->withPaginator($prices, new PriceTransformer(), ['key' => 'prices']);
    }

    /**
     * Show a single Price
     *
     * Get a payload of a single Price
     *
     * @Get("/{id}")
     */
    public function showCarrier($id) {

        $filter = new Filters([
            "carrierId" => ["eq" => $id]
            ]);
        
        $this->price->setFilters($filter);

        $prices = $this->price->byPage();
        
        $response = $this->response()->withPaginator($prices, new PriceTransformer(), ['key' => 'prices']);
    }

    /**
     * Show a single Price
     *
     * Get a payload of a single Price
     *
     * @Get("/{id}")
     */
    public function showCompany($id) {

        $filter = new Filters([
            "companyId" => ["eq" => $id]
            ]);
        
        $this->price->setFilters($filter);

        $prices = $this->price->byPage();
        
        $response = $this->response()->withPaginator($prices, new PriceTransformer(), ['key' => 'prices']);
    }



    /**
     * Update contents of a Price
     *
     * @param $id
     * @return \Dingo\Api\Http\Response
     */
    public function store($id, Request $request) {

        /*
         * Checks if Json has data, data-type & data-attributes.
         */
        if(!$this->isJsonCorrect($request, 'prices')){
            $error['errors']['json'] = 'Json is Invalid';
            return response()->json($error)->setStatusCode(409);
        } else {
            $data = $request->all()['data'];
            $dataAttributes = $data['attributes'];           
        }

        $dataAttributes['id'] = $id;
        $price = $this->price->update($dataAttributes);
        return $this->response()->item($price, new PriceTransformer(), ['key' => 'prices']);
    }

    /**
     * Create a new Price
     *
     * @return \Dingo\Api\Http\Response
     */
    public function create(Request $request) {
                if(!$this->isJsonCorrect($request, 'prices')){
            $error['errors']['json'] = 'Json is Invalid';
            return response()->json($error)->setStatusCode(409);
        } else {
            $data = $request->all()['data'];
            $dataAttributes = $data['attributes'];           
        }

        $price = $this->price->create($dataAttributes);
        return $this->response()->item($price, new PriceTransformer(), ['key' => 'prices']);
    }

    /**
     * Delete a Price
     *
     * @param $id
     */
    public function delete($id)
    {
        $price = Price::find($id);
        if($price <> null){
            $this->price->deleteById($id);
        } else {
            $error['errors']['delete'] = 'the price selected doesn\'t exists';   
            return response()->json($error)->setStatusCode(409);
        }
        
        $this->index();
        $price = Price::find($id);        
        if($price == null){
            return array("success" => true);
        } else {
            $error['errors']['delete'] = 'the price has not been deleted';   
            return response()->json($error)->setStatusCode(409);
        }
    }
}