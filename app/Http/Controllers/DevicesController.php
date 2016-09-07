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
        $success = true;
        $dataAssetsExists = false;
        $dataModificationsExists = false;
        $dataCarriersExists = false;
        $dataCompaniesExists = false;
        $dataPricesExists = false;
        /*
         * Checks if Json has data, data-type & data-attributes.
         */
        if(!$this->isJsonCorrect($request)){
            $error['errors']['json'] = 'Json is Invalid';
            return response()->json($error)->setStatusCode(409);
        } else {
            $data = $request->all()['data'];
            $dataType = $data['type'];
            $dataAttributes = $data['attributes'];           
        }

        DB::beginTransaction();

        $validator = Validator::make(array('image' => $dataAttributes['image']), ['image' => 'url']);
        if($validator->fails()){
            $error['errors']['image'] = 'The image file has not a valid format';
        }

        /*
         * Now we can create the Device.
         */       
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
            $error['errors']['devices'] = 'The Device can not be created';
            $error['errors']['devicesMessage'] = $this->getErrorAndParse($e);
            return response()->json($error)->setStatusCode(409);
        }

        /*
         * Check if Json has relationships to continue or if not and commit + return.
         */
        if(!isset($data['relationships'])){
            DB::commit();
            return $this->response()->item($device, new DeviceTransformer(), ['key' => 'devices']);
        } else {
            $dataRelationships = $data['relationships'];    

            if(isset($dataRelationships['assets'])){ 
                if(isset($dataRelationships['assets']['data'])){
                    $dataAssets = $this->parseJsonToArray($dataRelationships['assets']['data']);
                    $dataAssetsExists = true;
                }
            }
            if(isset($dataRelationships['modifications'])){ 
                if(isset($dataRelationships['modifications']['data'])){
                    $dataModifications = $this->parseJsonToArray($dataRelationships['modifications']['data']);
                    $dataModificationsExists = true;
                }
            }
            if(isset($dataRelationships['carriers'])){ 
                if(isset($dataRelationships['carriers']['data'])){
                    $dataCarriers = $this->parseJsonToArray($dataRelationships['carriers']['data']);
                    $dataCarriersExists = true;
                }
            }
            if(isset($dataRelationships['companies'])){ 
                if(isset($dataRelationships['companies']['data'])){
                    $dataCompanies = $this->parseJsonToArray($dataRelationships['companies']['data']);
                    $dataCompaniesExists = true;
                }
            }
            if(isset($dataRelationships['prices'])){ 
                if(isset($dataRelationships['prices']['data'])){
                    $dataPrices = $dataRelationships['prices']['data'];
                    $dataPricesExists = true;
                }
            }
        }  
        
        if($dataAssetsExists){
            try {
                $device->assets()->sync($dataAssets);    
            } catch (\Exception $e){
                DB::rollBack();
                $success = false;
                $error['errors']['assets'] = 'the Device Assets can not be created';
                $error['errors']['assetsMessage'] = $this->getErrorAndParse($e);
            }
        }

        if($dataModificationsExists){
            try {
                $device->modifications()->sync($dataModifications);
            } catch (\Exception $e){
                DB::rollBack();
                $success = false;
                $error['errors']['modifications'] = 'the Device Modifications can not be created';
                $error['errors']['modificationsMessage'] = $this->getErrorAndParse($e);
            }
        }

        if($dataCarriersExists){
            try {
                $device->carriers()->sync($dataCarriers);
            } catch (\Exception $e){
                DB::rollBack();
                $success = false;
                $error['errors']['carriers'] = 'the Device Carriers can not be created';
                $error['errors']['carriersMessage'] = $this->getErrorAndParse($e);
            }
        }

        if($dataCompaniesExists){
            try {
                $device->companies()->sync($dataCompanies);
            } catch (\Exception $e){
                DB::rollBack();
                $success = false;
                $error['errors']['companies'] = 'the Device Companies can not be created';
                $error['errors']['companiesMessage'] = $this->getErrorAndParse($e);
            }
        }

        if($dataPricesExists){
            try {
                $priceInterface = app()->make('WA\Repositories\Device\DevicePriceInterface');

                // Delete Equal Rows.
                $dataPrices = $this->deleteRepeat($dataPrices);

                foreach ($dataPrices as $price) {
                    if($this->checkIfPriceRowIsCorrect($price, $dataAssets, $dataModifications, $dataCarriers, $dataCompanies)){
                        $price['deviceId'] = $device->id;
                        $priceInterface->create($price);    
                    } else {
                        DB::rollBack();
                        $success = false;
                        $error['errors']['prices'] = 'the Device Prices can not be created';
                        $error['errors']['pricesMessage'] = 'Any price rows are not correct and no references provided sync.';
                    }                    
                }    
            } catch (\Exception $e) {
                DB::rollBack();
                $success = false;
                $error['errors']['prices'] = 'the Device Prices can not be created';
                $error['errors']['pricesMessage'] = $this->getErrorAndParse($e);
            }
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
        $success = true;
        $dataAssetsExists = false;
        $dataModificationsExists = false;
        $dataCarriersExists = false;
        $dataCompaniesExists = false;
        $dataPricesExists = false;
        /*
         * Checks if Json has data, data-type & data-attributes.
         */
        if(!$this->isJsonCorrect($request)){
            $error['errors']['json'] = 'Json is Invalid';
            return response()->json($error)->setStatusCode(409);
        } else {
            $data = $request->all()['data'];
            $dataType = $data['type'];
            $dataAttributes = $data['attributes'];           
        }

        DB::beginTransaction();

        $validator = Validator::make(array('image' => $dataAttributes['image']), ['image' => 'url']);
        if($validator->fails()){
            $error['errors']['image'] = 'The image file has not a valid format';
        }

        /*
         * Now we can create the Device.
         */       
        try{
            $device = $this->device->create($dataAttributes);
        } catch (\Exception $e) {
            DB::rollBack();
            $success = false;
            $error['errors']['devices'] = 'The Device can not be created';
            $error['errors']['devicesMessage'] = $this->getErrorAndParse($e);
            return response()->json($error)->setStatusCode(409);
        }

        /*
         * Check if Json has relationships to continue or if not and commit + return.
         */
        if(!isset($data['relationships'])){
            DB::commit();
            return $this->response()->item($device, new DeviceTransformer(), ['key' => 'devices']);
        } else {
            $dataRelationships = $data['relationships'];    

            if(isset($dataRelationships['assets'])){ 
                if(isset($dataRelationships['assets']['data'])){
                    $dataAssets = $this->parseJsonToArray($dataRelationships['assets']['data']);
                    $dataAssetsExists = true;
                }
            }
            if(isset($dataRelationships['modifications'])){ 
                if(isset($dataRelationships['modifications']['data'])){
                    $dataModifications = $this->parseJsonToArray($dataRelationships['modifications']['data']);
                    $dataModificationsExists = true;
                }
            }
            if(isset($dataRelationships['carriers'])){ 
                if(isset($dataRelationships['carriers']['data'])){
                    $dataCarriers = $this->parseJsonToArray($dataRelationships['carriers']['data']);
                    $dataCarriersExists = true;
                }
            }
            if(isset($dataRelationships['companies'])){ 
                if(isset($dataRelationships['companies']['data'])){
                    $dataCompanies = $this->parseJsonToArray($dataRelationships['companies']['data']);
                    $dataCompaniesExists = true;
                }
            }
            if(isset($dataRelationships['prices'])){ 
                if(isset($dataRelationships['prices']['data'])){
                    $dataPrices = $dataRelationships['prices']['data'];
                    $dataPricesExists = true;
                }
            }
        }  
        
        if($dataAssetsExists){
            try {
                $device->assets()->sync($dataAssets);    
            } catch (\Exception $e){
                DB::rollBack();
                $success = false;
                $error['errors']['assets'] = 'the Device Assets can not be created';
                $error['errors']['assetsMessage'] = $this->getErrorAndParse($e);
            }
        }

        if($dataModificationsExists){
            try {
                $device->modifications()->sync($dataModifications);
            } catch (\Exception $e){
                DB::rollBack();
                $success = false;
                $error['errors']['modifications'] = 'the Device Modifications can not be created';
                $error['errors']['modificationsMessage'] = $this->getErrorAndParse($e);
            }
        }

        if($dataCarriersExists){
            try {
                $device->carriers()->sync($dataCarriers);
            } catch (\Exception $e){
                DB::rollBack();
                $success = false;
                $error['errors']['carriers'] = 'the Device Carriers can not be created';
                $error['errors']['carriersMessage'] = $this->getErrorAndParse($e);
            }
        }

        if($dataCompaniesExists){
            try {
                $device->companies()->sync($dataCompanies);
            } catch (\Exception $e){
                DB::rollBack();
                $success = false;
                $error['errors']['companies'] = 'the Device Companies can not be created';
                $error['errors']['companiesMessage'] = $this->getErrorAndParse($e);
            }
        }

        if($dataPricesExists){
            try {
                $priceInterface = app()->make('WA\Repositories\Device\DevicePriceInterface');

                // Delete Equal Rows.
                $dataPrices = $this->deleteRepeat($dataPrices);

                foreach ($dataPrices as $price) {
                    if($this->checkIfPriceRowIsCorrect($price, $dataAssets, $dataModifications, $dataCarriers, $dataCompanies)){
                        $price['deviceId'] = $device->id;
                        $priceInterface->create($price);    
                    } else {
                        DB::rollBack();
                        $success = false;
                        $error['errors']['prices'] = 'the Device Prices can not be created';
                        $error['errors']['pricesMessage'] = 'Any price rows are not correct and no references provided sync.';
                    }                    
                }    
            } catch (\Exception $e) {
                DB::rollBack();
                $success = false;
                $error['errors']['prices'] = 'the Device Prices can not be created';
                $error['errors']['pricesMessage'] = $this->getErrorAndParse($e);
            }
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
    public function delete($id) {
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
            if(isset($info['id'])){
                array_push($array, $info['id']);    
            }            
        }        
        return $array;
    }

    private function getErrorAndParse($error){
        try{
            $reflectorResponse = new \ReflectionClass($error);
            $classResponse = $reflectorResponse->getProperty('message');    
            $classResponse->setAccessible(true);
            $dataResponse = $classResponse->getValue($error);
            return $dataResponse;    
        } catch (\Exception $e){
            return 'Generic Error';
        }
    }

    private function isJsonCorrect($request){
        if(!isset($request->all()['data'])){ 
            return false;
        } else {
            $data = $request->all()['data'];    
            if(!isset($data['type'])){ 
                return false; 
            }
            if(!isset($data['attributes'])){ 
                return false; 
            }
        }
        return true;
    }

    private function deleteRepeat($dataPrice){

        $keys = ['capacityId', 'styleId', 'carrierId', 'companyId'];
        $i = 1;
        $dataPriceAux = array();

        for ( $j = 0 ; $j < count($dataPrice) ; $j++){

            if($dataPriceAux == null){
                array_push($dataPriceAux, $dataPrice[$j]);
            } else {
                $save = true;

                for ( $k = 0 ; $k < count($dataPriceAux) ; $k++){
                    
                    $esIgual = true;
                    
                    if($dataPriceAux[$k]['capacityId'] <> $dataPrice[$j]['capacityId']){
                        $esIgual = $esIgual && false;
                    }
                    if($dataPriceAux[$k]['styleId'] <> $dataPrice[$j]['styleId']){
                        $esIgual = $esIgual && false;
                    }
                    if($dataPriceAux[$k]['carrierId'] <> $dataPrice[$j]['carrierId']){
                        $esIgual = $esIgual && false;
                    }
                    if($dataPriceAux[$k]['companyId'] <> $dataPrice[$j]['companyId']){
                        $esIgual = $esIgual && false;
                    }

                    if($esIgual){
                        $save = false;
                        break;
                    } else {
                        $save = $save && true;
                    }
                }

                if($save) {
                    array_push($dataPriceAux, $dataPrice[$j]);
                }
            }
        }
        return $dataPriceAux;
    }

    private function checkIfPriceRowIsCorrect($price, $assets, $modifications, $carriers, $companies){

        $modInterface = app()->make('WA\Repositories\Modification\ModificationInterface');
        
        if(isset($price['capacityId'])){
            $priceCapacityId = $price['capacityId'];
            
            foreach ($modifications as $mod) {

                $modification = $modInterface->byId($mod);
                $reflectorResponse = new \ReflectionClass($modification);
                $classResponse = $reflectorResponse->getProperty('attributes');    
                $classResponse->setAccessible(true);
                $dataResponse = $classResponse->getValue($modification);

                $idMod = $dataResponse['id'];               
                $typeMod = $dataResponse['type'];

                if($priceCapacityId == $idMod){
                    if($typeMod <> 'Capacity'){
                        return false;
                    }
                }
            }
        }

        if(isset($price['styleId'])){
            $priceStyleId = $price['styleId'];  

            foreach ($modifications as $mod) {

                $modification = $modInterface->byId($mod);
                $reflectorResponse = new \ReflectionClass($modification);
                $classResponse = $reflectorResponse->getProperty('attributes');    
                $classResponse->setAccessible(true);
                $dataResponse = $classResponse->getValue($modification);

                $idMod = $dataResponse['id'];
                $typeMod = $dataResponse['type'];

                if($priceStyleId == $idMod){

                    if($typeMod <> 'Style'){
                        return false;
                    }
                }
            }
        }

        $existsAsset = false;
        if(isset($price['assetId'])){
            $priceAssetId = $price['assetId'];  

            foreach ($assets as $as) {
                if($as == $priceAssetId){
                    $existsAsset = true;
                }
            }

            if(!$existsAsset){
                return false;
            }
        }

        $existsCarrier = false;
        if(isset($price['carrierId'])){
            $priceCarrierId = $price['carrierId'];  

            foreach ($carriers as $as) {
                if($as == $priceCarrierId){
                    $existsCarrier = true;
                }
            }

            if(!$existsCarrier){
                return false;
            }
        }

        $existsCompany = false;
        if(isset($price['companyId'])){
            $priceCompanyId = $price['companyId'];

            foreach ($companies as $as) {
                if($as == $priceCompanyId){
                    $existsCompany = true;
                }
            }
            
            if(!$existsCompany){
                return false;
            }
        }

        return true;
    }
}