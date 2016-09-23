<?php
namespace WA\Http\Controllers;

use WA\DataStore\Address\Address;
use WA\DataStore\Address\AddressTransformer;
use WA\Repositories\Address\AddressInterface;
use Illuminate\Http\Request;

use Log;

/**
 * Address resource.
 *
 * @Resource("address", uri="/address")
 */
class AddressController extends ApiController
{
    /**
     * @var AddressInterface
     */
    protected $address;

    /**
     * Address Controller constructor
     *
     * @param AddressInterface $address
     */
    public function __construct(AddressInterface $address)
    {
        $this->address = $address;
    }

    /**
     * Show all Address
     *
     * Get a payload of all Address
     *
     */
    public function index()
    {
        $criteria = $this->getRequestCriteria();
        $this->address->setCriteria($criteria);
        $address = $this->address->byPage();

        $response = $this->response()->withPaginator($address, new AddressTransformer(), ['key' => 'address']);
        $response = $this->applyMeta($response);
        return $response;
    }

    /**
     * Show a single Address
     *
     * Get a payload of a single Address
     *
     * @Get("/{id}")
     */
    public function show($id)
    {
        $address = Address::find($id);
        if($address == null){
            $error['errors']['get'] = 'the Address selected doesn\'t exists';   
            return response()->json($error)->setStatusCode(409);
        }

        return $this->response()->item($address, new AddressTransformer(), ['key' => 'address']);
    }

    /**
     * Update contents of a Address
     *
     * @param $id
     * @return \Dingo\Api\Http\Response
     */
    public function store($id, Request $request)   
    {
        $data = $request->all();       
        $data['id'] = $id;
        $address = $this->address->update($data);
        return $this->response()->item($address, new AddressTransformer(), ['key' => 'address']);
    }

    /**
     * Create a new Address
     *
     * @return \Dingo\Api\Http\Response
     */
    public function create(Request $request)
    {
        $data = $request->all();
        $address = $this->address->create($data);
        return $this->response()->item($address, new AddressTransformer(), ['key' => 'address']);
    }

    /**
     * Delete an Address
     *
     * @param $id
     */
    public function delete($id)
    {
        $address = Address::find($id);
        if($address <> null){
            $this->address->deleteById($id);
        } else {
            $error['errors']['delete'] = 'the Address selected doesn\'t exists';   
            return response()->json($error)->setStatusCode(409);
        }
        
        $this->index();
        $address = Address::find($id);
        if($address == null){
            return array("success" => true);
        } else {
            $error['errors']['delete'] = 'the Address has not been deleted';   
            return response()->json($error)->setStatusCode(409);
        }
    }
}