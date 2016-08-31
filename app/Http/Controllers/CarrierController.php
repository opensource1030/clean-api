<?php
namespace WA\Http\Controllers;

use Illuminate\Http\Request;
use WA\DataStore\Carrier\CarrierTransformer;
use WA\Repositories\Carrier\CarrierInterface;

/**
 * Carrier resource.
 *
 * @Resource("Carrier", uri="/Carrier")
 */
class CarrierController extends ApiController
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
    public function index()
    {
        $carrier = $this->carrier->byPage();
        return $this->response()->withPaginator($carrier, new CarrierTransformer(),['key' => 'carriers']);

    }

    /**
     * Show a single Carrier
     *
     * Get a payload of a single Carrier
     *
     * @Get("/{id}")
     */
    public function show($id)
    {
        $carrier = $this->carrier->byId($id);
        return $this->response()->item($carrier, new CarrierTransformer(), ['key' => 'carriers']);
    }

    /**
     * Update contents of a Carrier
     *
     * @param $id
     * @return \Dingo\Api\Http\Response
     */
    public function store($id, Request $request)   
    {
        $data = $request->all();       
        $data['id'] = $id;
        $carrier = $this->carrier->update($data);
        return $this->response()->item($carrier, new CarrierTransformer(), ['key' => 'carriers']);
    }

    /**
     * Create a new Carrier
     *
     * @return \Dingo\Api\Http\Response
     */
    public function create(Request $request)
    {
        $data = $request->all();
        $carrier = $this->carrier->create($data);
        return $this->response()->item($carrier, new CarrierTransformer(), ['key' => 'carriers']);
    }

    /**
     * Delete a Carrier
     *
     * @param $id
     */
    public function delete($id)
    {
        $this->carrier->deleteById($id);
        $this->index();
    }
}