<?php

namespace WA\Http\Controllers;

use Cartalyst\DataGrid\Laravel\Facades\DataGrid;
use Dingo\Api\Routing\Helpers;
use Illuminate\Session\SessionManager as Session;
use WA\DataStore\Device\DeviceTransformer;
use WA\DataStore\Device\Device;
use WA\Helpers\Traits\SetLimits;
use WA\Http\Controllers\Api\Traits\BasicCrud;
use Illuminate\Http\Request;

use WA\Repositories\Device\DeviceInterface;

use Validator;
use DB;
use Log;

/**
 * Devices resource.
 *
 * @Resource("Devices", uri="/devices")
 */
class DevicesController extends ApiController
{
    /**
     * @var DeviceInterface
     */
    protected $device;

    /**
     * Package Controller constructor
     *
     * @param DeviceInterface $device
     */
    public function __construct(DeviceInterface $device)
    {
        $this->device = $device;
    }

    /**
     * Show all devices
     *
     * Get a payload of all devices
     *
     * @Get("/")
     * @Parameters({
     *      @Parameter("page", description="The page of results to view.", default=1),
     *      @Parameter("limit", description="The amount of results per page.", default=10),
     *      @Parameter("access_token", required=true, description="Access token for authentication")
     * })
     */
    public function index()
    {
        $devices = $this->device->byPage();

        return $this->response()->withPaginator($devices, new DeviceTransformer(),['key' => 'devices']);
    }

    /**
     * Show a single users
     *
     * Get a payload of a single devices by it's ID
     *
     * @Get("/{id}")
     */
    public function show($id)
    {
        $device = Device::find($id);
        if($device == null){
            $error['errors']['get'] = 'the Device selected doesn\'t exists';   
            return response()->json($error)->setStatusCode(409);
        }
        
        return $this->response()->item($device, new DeviceTransformer(),['key' => 'devices']);
    }


    public function datatable()
    {

        $this->setLimits();

        $devices = $this->model->getDataTable();
        $columns = [
            'devices.id' => 'id',
            'devices.identification' => 'identification',
            'device_types.make' => 'make',
            'device_types.model' => 'model',
            'device_types.class' => 'class',
        ];

        $options = [
            'throttle' => $this->defaultQueryParams['_perPage'],
            'method' => $this->defaultQueryParams['_method'],
        ];

        $this->setLimits();

        $response = DataGrid::make($devices, $columns, $options);

        return $response;
    }

    /**
     * Update contents of a device
     *
     * @param $id
     * @return \Dingo\Api\Http\Response
     */
    public function store($id, Request $request)   
    {
        $data = $request->all()['data'];
        $success = true;

        $dataType = $data['type'];
        $dataAttributes = $data['attributes'];
        $dataRelationships = $data['relationships'];
        $dataAssets = $this->parseJsonToArray($dataRelationships['assets']['data']);
        $dataModifications = $this->parseJsonToArray($dataRelationships['modifications']['data']);
        $dataCarriers = $this->parseJsonToArray($dataRelationships['carriers']['data']);
        $dataCompanies = $this->parseJsonToArray($dataRelationships['companies']['data']);
        $dataPrices = $dataRelationships['prices']['data'];

        DB::beginTransaction();

        $validator = Validator::make(array('image' => $dataAttributes['image']), ['image' => 'url']);
        if($validator->fails()){
            $error['errors']['image'] = 'The image file has not a valid format';
        }
        
        try{
            $device = Device::find($id);

            $device->image = $dataAttributes['image'];
            $device->name = $dataAttributes['name'];
            $device->properties = $dataAttributes['properties'];
            $device->deviceTypeId = $dataAttributes['deviceTypeId'];

            $device->save();
            
        } catch (\Exception $e) {
            DB::rollBack();
            $success = false;
            $error['errors']['devices'] = 'The Device can not be updated';
            return response()->json($error)->setStatusCode(409);
        }

        try {
            $device->assets()->sync($dataAssets);    
        } catch (\Exception $e){
            DB::rollBack();
            $success = false;
            $error['errors']['assets'] = 'the Device Assets can not be updated';
        }

        try {
            $device->modifications()->sync($dataModifications);
        } catch (\Exception $e){
            DB::rollBack();
            $success = false;
            $error['errors']['modifications'] = 'the Device Modifications can not be updated';
        }

        try {
            $device->carriers()->sync($dataCarriers);
        } catch (\Exception $e){
            DB::rollBack();
            $success = false;
            $error['errors']['carriers'] = 'the Device Carriers can not be updated';
        }

        try {
            $device->companies()->sync($dataCompanies);
        } catch (\Exception $e){
            DB::rollBack();
            $success = false;
            $error['errors']['companies'] = 'the Device Companies can not be updated';
        }
        
        try{
            $priceInterface = app()->make('WA\Repositories\Device\DevicePriceInterface');

            foreach ($dataPrices as $price) {
                $price['deviceId'] = $device->id;
                $priceInterface->update($price);
            }    

        } catch (\Exception $e) {
            DB::rollBack();
            $success = false;
            $error['errors']['prices'] = 'the Device Prices can not be updated';
        }

        if(!$success){
            DB::rollBack();
            return response()->json($error)->setStatusCode(409);
        } else {
            DB::commit();
            return $this->response()->item($device, new DeviceTransformer(), ['key' => 'devices']);
        }       
    }

    /**
     * Create a new device
     *
     * @return \Dingo\Api\Http\Response
     */
    public function create(Request $request)
    {        
        $data = $request->all()['data'];
        $success = true;

        $dataType = $data['type'];
        $dataAttributes = $data['attributes'];
        $dataRelationships = $data['relationships'];
        $dataAssets = $this->parseJsonToArray($dataRelationships['assets']['data']);
        $dataModifications = $this->parseJsonToArray($dataRelationships['modifications']['data']);
        $dataCarriers = $this->parseJsonToArray($dataRelationships['carriers']['data']);
        $dataCompanies = $this->parseJsonToArray($dataRelationships['companies']['data']);
        $dataPrices = $dataRelationships['prices']['data'];
        
        DB::beginTransaction();

        $validator = Validator::make(array('image' => $dataAttributes['image']), ['image' => 'url']);
        if($validator->fails()){
            $error['errors']['image'] = 'The image file has not a valid format';
        }

        try{
            $device = $this->device->create($dataAttributes);

        } catch (\Exception $e) {
            Log::info("Exception dataAttributes: ".var_dump($e));
            DB::rollBack();
            $success = false;
            $error['errors']['devices'] = 'The Device can not be created';
            return response()->json($error)->setStatusCode(409);
        }

        try {
            $device->assets()->sync($dataAssets);    
        } catch (\Exception $e){
            Log::info("Exception dataAssets: ".var_dump($e));
            DB::rollBack();
            $success = false;
            $error['errors']['assets'] = 'the Device Assets can not be created';
        }

        try {
            $device->modifications()->sync($dataModifications);
        } catch (\Exception $e){
            Log::info("Exception dataModifications: ".var_dump($e));
            DB::rollBack();
            $success = false;
            $error['errors']['modifications'] = 'the Device Modifications can not be created';
        }

        try {
            $device->carriers()->sync($dataCarriers);
        } catch (\Exception $e){
            Log::info("Exception dataCarriers: ".var_dump($e));
            DB::rollBack();
            $success = false;
            $error['errors']['carriers'] = 'the Device Carriers can not be created';
        }

        try {
            $device->companies()->sync($dataCompanies);
        } catch (\Exception $e){
            Log::info("Exception dataCompanies: ".var_dump($e));
            DB::rollBack();
            $success = false;
            $error['errors']['companies'] = 'the Device Companies can not be created';
        }
        
        try{
            $priceInterface = app()->make('WA\Repositories\Device\DevicePriceInterface');

            foreach ($dataPrices as $price) {
                $price['deviceId'] = $device->id;
                $priceInterface->create($price);
            }    

        } catch (\Exception $e) {
            Log::info("Exception dataPrices: ".var_dump($e));
            DB::rollBack();
            $success = false;
            $error['errors']['prices'] = 'the Device Prices can not be created';
        }

        if(!$success){
            DB::rollBack();
            return response()->json($error)->setStatusCode(409);
        } else {
            DB::commit();
            return $this->response()->item($device, new DeviceTransformer(), ['key' => 'devices']);
        }
    }

    /**
     * Delete a device
     *
     * @param $id
     */
    public function delete($id)
    {
        $device = Device::find($id);
        if($device <> null){
            $this->device->deleteById($id);
        } else {
            $error['errors']['delete'] = 'the Device selected doesn\'t exists';   
            return response()->json($error)->setStatusCode(409);
        }
        
        $this->index();
        $device = Device::find($id);        
        if($device == null){
            return array("success" => true);
        } else {
            $error['errors']['delete'] = 'the Device has not been deleted';   
            return response()->json($error)->setStatusCode(409);
        }
    }

    private function parseJsonToArray($data){
        $array = array();
        
        foreach ($data as $info) {
            array_push($array, $info['id']);
        }
        
        return $array;
    }
}