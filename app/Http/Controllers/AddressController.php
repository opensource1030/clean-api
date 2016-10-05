<?php

namespace WA\Http\Controllers;

use Illuminate\Http\Request;
use WA\DataStore\Address\Address;
use WA\DataStore\Address\AddressTransformer;
use WA\Repositories\Address\AddressInterface;

use Illuminate\Support\Facades\Lang;

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
        $criteria = $this->getRequestCriteria();
        $this->address->setCriteria($criteria);
        $address = Address::find($id);

        if($address == null){
            $error['errors']['get'] = Lang::get('messages.NotExistClass', ['class' => 'Address']);   
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }

        return $this->response()->item($address, new AddressTransformer(), ['key' => 'address'])->setStatusCode($this->status_codes['created']);
    }

    /**
     * Update contents of an Address
     *
     * @param $id
     * @return \Dingo\Api\Http\Response
     */
    public function store($id, Request $request)
    {
        if ($this->isJsonCorrect($request, 'address')) {
            try {
                
                $data = $request->all()['data']['attributes'];
                $data['id'] = $id;
                $address = $this->address->update($data);

                return $this->response()->item($address, new AddressTransformer(), ['key' => 'address'])->setStatusCode($this->status_codes['created']);
            } catch (\Exception $e){
                $error['errors']['address'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'Address', 'option' => 'updated', 'include' => '']);
                //$error['errors']['addressMessage'] = $e->getMessage();
            }
        } else {
            $error['errors']['json'] = Lang::get('messages.InvalidJson');
        }

        return response()->json($error)->setStatusCode($this->status_codes['conflict']);
    }

    /**
     * Create a new Address
     *
     * @return \Dingo\Api\Http\Response
     */
    public function create(Request $request)
    {
        if ($this->isJsonCorrect($request, 'address')) {
            try {

                $data = $request->all()['data']['attributes'];
                $address = $this->address->create($data);

                return $this->response()->item($address, new AddressTransformer(), ['key' => 'address'])->setStatusCode($this->status_codes['created']);
            } catch (\Exception $e){
                $error['errors']['address'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'Address', 'option' => 'created', 'include' => '']);
                //$error['errors']['addressMessage'] = $e->getMessage();
            }
        } else {
            $error['errors']['json'] = Lang::get('messages.InvalidJson');
        }

        return response()->json($error)->setStatusCode($this->status_codes['conflict']);
    }

    /**
     * Delete a Address
     *
     * @param $id
     */
    public function delete($id)
    {
        $address = Address::find($id);
        if ($address <> null) {
            $this->address->deleteById($id);
        } else {
            $error['errors']['delete'] = Lang::get('messages.NotExistClass', ['class' => 'Address']);   
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }
        
        $address = Address::find($id);        
        if($address == null){
            return array("success" => true);
        } else {
            $error['errors']['delete'] = Lang::get('messages.NotDeletedClass', ['class' => 'Address']);   
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }
    }
}