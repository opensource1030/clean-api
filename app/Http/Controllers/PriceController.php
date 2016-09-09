<?php
namespace WA\Http\Controllers;

use Price;
use WA\DataStore\Price\PriceTransformer;
use WA\Repositories\Price\PriceInterface;
use Illuminate\Http\Request;

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
        $price = $this->price->byPage();
        return $this->response()->withPaginator($price, new PriceTransformer(),['key' => 'prices']);

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
        $price = $this->price->byId($id);
        return $this->response()->item($price, new PriceTransformer(), ['key' => 'prices']);
    }

    /**
     * Update contents of a Price
     *
     * @param $id
     * @return \Dingo\Api\Http\Response
     */
    public function store($id, Request $request)   
    {
        $data = $request->all();       
        $data['id'] = $id;
        $price = $this->price->update($data);
        return $this->response()->item($price, new PriceTransformer(), ['key' => 'prices']);
    }

    /**
     * Create a new Price
     *
     * @return \Dingo\Api\Http\Response
     */
    public function create(Request $request)
    {
        $data = $request->all();
        $price = $this->price->create($data);
        return $this->response()->item($price, new PriceTransformer(), ['key' => 'prices']);
    }

    /**
     * Delete a Price
     *
     * @param $id
     */
    public function delete($id)
    {
        $this->price->deleteById($id);
        $this->index();
    }
}