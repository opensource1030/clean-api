<?php
namespace WA\Http\Controllers;

use Illuminate\Http\Request;
use WA\DataStore\Carrier\Carrier;
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
    public function index() {

        $criteria = $this->getRequestCriteria();
        $this->carrier->setCriteria($criteria);
        $carrier = $this->carrier->byPage();
      
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
    public function show($id)
    {
        $carrier = Carrier::find($id);
        if($carrier == null){
            $error['errors']['get'] = 'the carrier selected doesn\'t exists';   
            return response()->json($error)->setStatusCode(409);
        }

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
        /*
         * Checks if Json has data, data-type & data-attributes.
         */
        if(!$this->isJsonCorrect($request, 'carriers')){
            $error['errors']['json'] = 'Json is Invalid';
            return response()->json($error)->setStatusCode(409);
        } else {
            $data = $request->all()['data'];
            $dataAttributes = $data['attributes'];           
        }

        $dataAttributes['id'] = $id;
        $carrier = $this->carrier->update($dataAttributes);
        return $this->response()->item($carrier, new CarrierTransformer(), ['key' => 'carriers']);
    }

    /**
     * Create a new Carrier
     *
     * @return \Dingo\Api\Http\Response
     */
    public function create(Request $request)
    {
        if(!$this->isJsonCorrect($request, 'carriers')){
            $error['errors']['json'] = 'Json is Invalid';
            return response()->json($error)->setStatusCode(409);
        } else {
            $data = $request->all()['data'];
            $dataAttributes = $data['attributes'];           
        }

        $carrier = $this->carrier->create($dataAttributes);
        return $this->response()->item($carrier, new CarrierTransformer(), ['key' => 'carriers']);
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
            $error['errors']['delete'] = 'the carrier selected doesn\'t exists';   
            return response()->json($error)->setStatusCode(409);
        }
        
        $this->index();
        $carrier = Carrier::find($id);        
        if($carrier == null){
            return array("success" => true);
        } else {
            $error['errors']['delete'] = 'the carrier has not been deleted';   
            return response()->json($error)->setStatusCode(409);
        }
    }
}