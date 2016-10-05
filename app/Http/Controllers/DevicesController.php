<?php

namespace WA\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Validator;
use WA\DataStore\Device\Device;
use WA\DataStore\Device\DeviceTransformer;
use WA\Repositories\Device\DeviceInterface;

use Illuminate\Support\Facades\Lang;

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

    public function index(Request $request) {

        $criteria = $this->getRequestCriteria();
        $this->device->setCriteria($criteria);
        $device = $this->device->byPage();

        if(!$this->includesAreCorrect($request, new DeviceTransformer())){
            $error['errors']['getIncludes'] = Lang::get('messages.NotExistClass', ['class' => 'Device']);
            return response()->json($error)->setStatusCode($this->status_codes['badrequest']);
        }
      
        $response = $this->response()->withPaginator($device, new DeviceTransformer(),['key' => 'devices']);

        $response = $this->applyMeta($response);
        return $response;
    }

    /**
     * Show a single users
     *
     * Get a payload of a single devices by it's ID
     *
     * @Get("/{id}")
     */
    public function show($id, Request $request)
    {

        $criteria = $this->getRequestCriteria();
        $this->device->setCriteria($criteria);

        $device = Device::find($id);
        if($device == null){
            $error['errors']['get'] = Lang::get('messages.NotExistClass', ['class' => 'Device']);   
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }

        if(!$this->includesAreCorrect($request, new DeviceTransformer())){
            $error['errors']['getIncludes'] = Lang::get('messages.NotExistInclude');
            return response()->json($error)->setStatusCode($this->status_codes['badrequest']);
        }

        return $this->response()->item($device, new DeviceTransformer(),
            ['key' => 'devices'])->setStatusCode($this->status_codes['created']);
    }

    public function datatable()
    {

        $this->setLimits();

        $devices = $this->model->getDataTable();
        $columns = [
            'devices.id'             => 'id',
            'devices.identification' => 'identification',
            'device_types.make'      => 'make',
            'device_types.model'     => 'model',
            'device_types.class'     => 'class',
        ];

        $options = [
            'throttle' => $this->defaultQueryParams['_perPage'],
            'method'   => $this->defaultQueryParams['_method'],
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
        $dataImages = $dataAssets = $dataModifications = $dataCarriers = $dataCompanies = array();

        /*
         * Checks if Json has data, data-type & data-attributes.
         */
        if(!$this->isJsonCorrect($request, 'devices')){
            $error['errors']['json'] = Lang::get('messages.InvalidJson');
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        } else {
            $data = $request->all()['data'];
            $dataType = $data['type'];
            $dataAttributes = $data['attributes'];
        }

        DB::beginTransaction();

        /*
         * Now we can update the Device.
         */
        try {
            $device = Device::find($id);

            if($device == null) {
                $error['errors']['devices'] = Lang::get('messages.NotExistClass', ['class' => 'Device']);
                //$error['errors']['devicesMessage'] = $e->getMessage();
                return response()->json($error)->setStatusCode($this->status_codes['notexists']);
            }

            $device->name = isset($dataAttributes['name']) ? $dataAttributes['name'] : $device->name;
            $device->properties = isset($dataAttributes['properties']) ? $dataAttributes['properties'] : $device->properties;
            $device->deviceTypeId = isset($dataAttributes['deviceTypeId']) ? $dataAttributes['deviceTypeId'] : $device->deviceTypeId;
            $device->statusId = isset($dataAttributes['statusId']) ? $dataAttributes['statusId'] : $device->statusId;
            $device->externalId = isset($dataAttributes['externalId']) ? $dataAttributes['externalId'] : $device->externalId;
            $device->identification = isset($dataAttributes['identification']) ? $dataAttributes['identification'] : $device->identification;
            $device->syncId = isset($dataAttributes['syncId']) ? $dataAttributes['syncId'] : $device->syncId;

            $device->save();
        } catch (\Exception $e) {
            DB::rollBack();
            $error['errors']['devices'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'Device', 'option' => 'updated', 'include' => '']);
            //$error['errors']['devicesMessage'] = $e->getMessage();
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }

        /*
         * Check if Json has relationships to continue or if not and commit + return.
         */
        if (isset($data['relationships'])) {

            $dataRelationships = $data['relationships'];

            if (isset($dataRelationships['images'])) {
                if (isset($dataRelationships['images']['data'])) {
                    $dataImages = $this->parseJsonToArray($dataRelationships['images']['data'], 'images');
                    try {
                        $device->images()->sync($dataImages);    
                    } catch (\Exception $e){
                        $error['errors']['images'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'Device', 'option' => 'updated', 'include' => 'Images']);
                        //$error['errors']['imagesMessage'] = $e->getMessage();
                    }
                }
            }

            if (isset($dataRelationships['assets'])) {
                if (isset($dataRelationships['assets']['data'])) {
                    $dataAssets = $this->parseJsonToArray($dataRelationships['assets']['data'], 'assets');
                    try {
                        $device->assets()->sync($dataAssets);    
                    } catch (\Exception $e){
                        $error['errors']['assets'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'Device', 'option' => 'updated', 'include' => 'Assets']);
                        //$error['errors']['assetsMessage'] = $e->getMessage();
                    }
                }
            }

            if (isset($dataRelationships['modifications'])) {
                if (isset($dataRelationships['modifications']['data'])) {
                    $dataModifications = $this->parseJsonToArray($dataRelationships['modifications']['data'],
                        'modifications');
                    try {
                        $device->modifications()->sync($dataModifications);
                    } catch (\Exception $e) {
                        $success = false;
                        $error['errors']['modifications'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'Device', 'option' => 'updated', 'include' => 'Modifications']);
                        //$error['errors']['modificationsMessage'] = $e->getMessage();
                    }
                }
            }

            if (isset($dataRelationships['carriers'])) {
                if (isset($dataRelationships['carriers']['data'])) {
                    $dataCarriers = $this->parseJsonToArray($dataRelationships['carriers']['data'], 'carriers');
                    try {
                        $device->carriers()->sync($dataCarriers);
                    } catch (\Exception $e) {
                        $success = false;
                        $error['errors']['carriers'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'Device', 'option' => 'updated', 'include' => 'Carriers']);
                        //$error['errors']['carriersMessage'] = $e->getMessage();
                    }
                }
            }

            if (isset($dataRelationships['companies'])) {
                if (isset($dataRelationships['companies']['data'])) {
                    $dataCompanies = $this->parseJsonToArray($dataRelationships['companies']['data'], 'companies');
                    try {
                        $device->companies()->sync($dataCompanies);
                    } catch (\Exception $e) {
                        $success = false;
                        $error['errors']['companies'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'Device', 'option' => 'updated', 'include' => 'Companies']);
                        //$error['errors']['companiesMessage'] = $e->getMessage();
                    }
                }
            }

            if (isset($dataRelationships['prices'])) {
                if (isset($dataRelationships['prices']['data'])) {
                    $dataPrices = $dataRelationships['prices']['data'];

                    if ($success) {
                        try {
                            $priceInterface = app()->make('WA\Repositories\Price\PriceInterface');

                            $dataPrices = $this->deleteRepeat($dataPrices);

                            foreach ($dataPrices as $price) {
                                $check = $this->checkIfPriceRowIsCorrect($price, $dataModifications, $dataCarriers,
                                    $dataCompanies);
                                if ($check['bool']) {
                                    $price['deviceId'] = $device->id;

                                    if(isset($price['id'])){
                                        if($price['id'] == 0){
                                            $priceInterface->create($price);
                                        } else if ($price['id'] > 0 ){
                                            $priceInterface->update($price);
                                        } else {
                                            $success = false;
                                            $error['errors']['prices'] = 'the Price has an incorrect id';
                                        }
                                    } else {
                                        $success = false;
                                        $error['errors']['prices'] = 'the Price has no id';
                                    }
                                            
                                    $priceInterface->create($price);

                                } else {
                                    $success = false;
                                    $error['errors']['prices'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'Device', 'option' => 'updated', 'include' => 'Prices']);
                                    //$error['errors']['pricesCheck'] = $check['error'];
                                    //$error['errors']['pricesIdError'] = $check['id'];
                                    //$error['errors']['pricesMessage'] = 'Any price rows are not correct and no references provided relationships.';
                                }
                            }
                        } catch (\Exception $e) {
                            $success = false;
                            $error['errors']['prices'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'Device', 'option' => 'updated', 'include' => 'Prices']);
                            //$error['errors']['pricesMessage'] = $e->getMessage();
                        }
                    } else {
                        $success = false;
                        $error['errors']['prices'] = Lang::get('messages.NotIncludeExistsOptionClass', ['class' => 'Device', 'option' => 'updated', 'include' => 'Prices']);
                        //$error['errors']['pricesMessage'] = $e->getMessage();
                    }
                }
            }
        }

        if($success){
            DB::commit();
            return $this->response()->item($device, new DeviceTransformer(), ['key' => 'devices'])->setStatusCode($this->status_codes['created']);
        } else {
            DB::rollBack();
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
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
        $dataImages = $dataAssets = $dataModifications = $dataCarriers = $dataCompanies = array();

        /*
         * Checks if Json has data, data-type & data-attributes.
         */
        if(!$this->isJsonCorrect($request, 'devices')){
            $error['errors']['json'] = Lang::get('messages.InvalidJson');
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        } else {
            $data = $request->all()['data'];
            $dataType = $data['type'];
            $dataAttributes = $data['attributes'];
        }

        DB::beginTransaction();

        /*
         * Now we can create the Device.
         */
        try {
            $device = $this->device->create($dataAttributes);
        } catch (\Exception $e) {
            DB::rollBack();
            $error['errors']['devices'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'Device', 'option' => 'created', 'include' => '']);
            //$error['errors']['devicesMessage'] = $e->getMessage();
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }

        /*
         * Check if Json has relationships to continue or if not and commit + return.
         */
        if (isset($data['relationships'])) {

            $dataRelationships = $data['relationships'];

            if (isset($dataRelationships['images'])) {
                if (isset($dataRelationships['images']['data'])) {
                    $dataImages = $this->parseJsonToArray($dataRelationships['images']['data'], 'images');
                    try {
                        $device->images()->sync($dataImages);    
                    } catch (\Exception $e){
                        $error['errors']['images'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'Device', 'option' => 'created', 'include' => 'Images']);
                        //$error['errors']['imagesMessage'] = $e->getMessage();
                    }
                }
            }

            if (isset($dataRelationships['assets'])) {
                if (isset($dataRelationships['assets']['data'])) {
                    $dataAssets = $this->parseJsonToArray($dataRelationships['assets']['data'], 'assets');
                    try {
                        $device->assets()->sync($dataAssets);    
                    } catch (\Exception $e){
                        $error['errors']['assets'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'Device', 'option' => 'created', 'Assets' => '']);
                        //$error['errors']['assetsMessage'] = $e->getMessage();
                    }
                }
            }

            if (isset($dataRelationships['modifications'])) {
                if (isset($dataRelationships['modifications']['data'])) {
                    $dataModifications = $this->parseJsonToArray($dataRelationships['modifications']['data'],
                        'modifications');
                    try {
                        $device->modifications()->sync($dataModifications);
                    } catch (\Exception $e) {
                        $success = false;
                        $error['errors']['modifications'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'Device', 'option' => 'created', 'include' => 'Modifications']);
                        //$error['errors']['modificationsMessage'] = $e->getMessage();
                    }
                }
            }

            if (isset($dataRelationships['carriers'])) {
                if (isset($dataRelationships['carriers']['data'])) {
                    $dataCarriers = $this->parseJsonToArray($dataRelationships['carriers']['data'], 'carriers');
                    try {
                        $device->carriers()->sync($dataCarriers);
                    } catch (\Exception $e) {
                        $success = false;
                        $error['errors']['carriers'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'Device', 'option' => 'created', 'include' => 'Carriers']);
                        //$error['errors']['carriersMessage'] = $e->getMessage();
                    }
                }
            }

            if (isset($dataRelationships['companies'])) {
                if (isset($dataRelationships['companies']['data'])) {
                    $dataCompanies = $this->parseJsonToArray($dataRelationships['companies']['data'], 'companies');
                    try {
                        $device->companies()->sync($dataCompanies);
                    } catch (\Exception $e) {
                        $success = false;
                        $error['errors']['companies'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'Device', 'option' => 'created', 'include' => 'Companies']);
                        //$error['errors']['companiesMessage'] = $e->getMessage();
                    }
                }
            }

            if (isset($dataRelationships['prices'])) {
                if (isset($dataRelationships['prices']['data'])) {
                    $dataPrices = $dataRelationships['prices']['data'];

                    if ($success) {
                        try {
                            $priceInterface = app()->make('WA\Repositories\Price\PriceInterface');

                            $dataPrices = $this->deleteRepeat($dataPrices);

                            foreach ($dataPrices as $price) {
                                $check = $this->checkIfPriceRowIsCorrect($price, $dataModifications, $dataCarriers,
                                    $dataCompanies);
                                if ($check['bool']) {
                                    $price['deviceId'] = $device->id;
                                    $priceInterface->create($price);
                                } else {
                                    $success = false;
                                    $error['errors']['prices'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'Device', 'option' => 'created', 'include' => 'Prices']);
                                    //$error['errors']['pricesCheck'] = $check['error'];
                                    //$error['errors']['pricesIdError'] = $check['id'];
                                    //$error['errors']['pricesMessage'] = 'Any price rows are not correct and no references provided relationships.';
                                }
                            }
                        } catch (\Exception $e) {
                            $success = false;
                            $error['errors']['prices'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'Device', 'option' => 'created', 'include' => 'Prices']);
                            //$error['errors']['pricesMessage'] = $e->getMessage();
                        }
                    } else {
                        $success = false;
                        $error['errors']['prices'] = Lang::get('messages.NotIncludeExistsOptionClass', ['class' => 'Device', 'option' => 'created', 'include' => 'Prices']);
                        $error['errors']['pricesMessage'] = $e->getMessage();
                    }
                }
            }
        }

        if($success){
            DB::commit();
            return $this->response()->item($device, new DeviceTransformer(), ['key' => 'devices'])->setStatusCode($this->status_codes['created']);
        } else {
            DB::rollBack();
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
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
        if ($device <> null) {
            $this->device->deleteById($id);
        } else {
            $error['errors']['delete'] = Lang::get('messages.NotExistClass', ['class' => 'Device']);   
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }
        
        $device = Device::find($id);        
        if($device == null){
            return array("success" => true);
        } else {
            $error['errors']['delete'] = Lang::get('messages.NotDeletedClass', ['class' => 'Device']);   
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }
    }

    /*
     *
     * PRIVATE FUNCTIONS
     *
     */

    /*
     *      Checks if an ARRAY has repeated rows and returns an ARRAY without them.
     *
     *      @param: 
     *          "prices" : {
     *              "data" : [
     *                  {
     *                      "type": "prices",
     *                      "capacityId": 1,
     *                      "styleId": 2,
     *                      "carrierId": 1,
     *                      "companyId": 1,
     *                      "priceRetail": 100,
     *                      "price1": 100,
     *                      "price2": 100,
     *                      "priceOwn": 100
     *                  },
     *                  {
     *                      "type": "prices",
     *                      "capacityId": 1,
     *                      "styleId": 2,
     *                      "carrierId": 1,
     *                      "companyId": 1,
     *                      "priceRetail": 100,
     *                      "price1": 100,
     *                      "price2": 100,
     *                      "priceOwn": 100
     *                  },
     *                  ...
     *      @return: array(
     *                  {
     *                      "type": "prices",
     *                      "capacityId": 1,
     *                      "styleId": 2,
     *                      "carrierId": 1,
     *                      "companyId": 1,
     *                      "priceRetail": 100,
     *                      "price1": 100,
     *                      "price2": 100,
     *                      "priceOwn": 100
     *                  },
     *                  ...
     */
    private function deleteRepeat($data)
    {

        $dataAux = array();

        for ($j = 0; $j < count($data); $j++) {

            if ($dataAux == null) {
                array_push($dataAux, $data[$j]);
            } else {
                $save = true;

                for ($k = 0; $k < count($dataAux); $k++) {

                    $esIgual = true;

                    if ($dataAux[$k]['capacityId'] <> $data[$j]['capacityId']) {
                        $esIgual = $esIgual && false;
                    }
                    if ($dataAux[$k]['styleId'] <> $data[$j]['styleId']) {
                        $esIgual = $esIgual && false;
                    }
                    if ($dataAux[$k]['carrierId'] <> $data[$j]['carrierId']) {
                        $esIgual = $esIgual && false;
                    }
                    if ($dataAux[$k]['companyId'] <> $data[$j]['companyId']) {
                        $esIgual = $esIgual && false;
                    }

                    if ($esIgual) {
                        $save = false;
                        break;
                    } else {
                        $save = $save && true;
                    }
                }

                if ($save) {
                    array_push($dataAux, $data[$j]);
                }
            }
        }
        return $dataAux;
    }

    /*
     *      Checks if an ARRAY param of Prices has information that is equal to the other information provided.
     *
     *      @param: 
     *          array (size=9) (prices)
     *              'type' => string 'prices' (length=6)
     *              'capacityId' => int 1
     *              'styleId' => int 2
     *              'carrierId' => int 1
     *              'companyId' => int 1
     *              'priceRetail' => int 100
     *              'price1' => int 100
     *              'price2' => int 100
     *              'priceOwn' => int 100
     *          array (size=3) (modifications)
     *              0 => int 1
     *              1 => int 2
     *              2 => int 3
     *          array (size=2) (carriers)
     *              0 => int 1
     *              1 => int 2
     *          array (size=2) (companies)
     *              0 => int 1
     *              1 => int 2
     *      @return:
     *          array (size=3)
     *              'bool' => boolean true
     *              'error' => string 'No Error' (length=8)
     *              'id' => int 0
     */
    private function checkIfPriceRowIsCorrect($price, $modifications, $carriers, $companies)
    {

        $modInterface = app()->make('WA\Repositories\Modification\ModificationInterface');

        $existsCapacity = false;
        if (isset($price['capacityId'])) {

            foreach ($modifications as $mod) {

                $modification = $modInterface->byId($mod);
                $reflectorResponse = new \ReflectionClass($modification);
                $classResponse = $reflectorResponse->getProperty('attributes');
                $classResponse->setAccessible(true);
                $dataResponse = $classResponse->getValue($modification);

                if ($price['capacityId'] == $dataResponse['id']) {
                    if ($dataResponse['modType'] == 'capacity') {
                        $existsCapacity = true;
                    }
                }
            }

            if (!$existsCapacity) {
                return array("bool" => false, "error" => "Capacity Not Found", "id" => $price['capacityId']);
            }
        }


        $existsStyle = false;
        if (isset($price['styleId'])) {

            foreach ($modifications as $mod) {

                $modification = $modInterface->byId($mod);
                $reflectorResponse = new \ReflectionClass($modification);
                $classResponse = $reflectorResponse->getProperty('attributes');
                $classResponse->setAccessible(true);
                $dataResponse = $classResponse->getValue($modification);

                if ($price['styleId'] == $dataResponse['id']) {
                    if ($dataResponse['modType'] == 'style') {
                        $existsStyle = true;
                    }
                }
            }

            if (!$existsStyle) {
                return array("bool" => false, "error" => "Style Not Found", "id" => $price['styleId']);
            }
        }

        $existsCarrier = false;
        if (isset($price['carrierId'])) {

            foreach ($carriers as $as) {
                if ($as == $price['carrierId']) {
                    $existsCarrier = true;
                }
            }

            if (!$existsCarrier) {
                return array("bool" => false, "error" => "Carrier Not Found", "id" => $price['carrierId']);
            }
        }

        $existsCompany = false;
        if (isset($price['companyId'])) {

            foreach ($companies as $as) {
                if ($as == $price['companyId']) {
                    $existsCompany = true;
                }
            }

            if (!$existsCompany) {
                return array("bool" => false, "error" => "Company Not Found", "id" => $price['companyId']);
            }
        }

        return array("bool" => true, "error" => "No Error", "id" => 0);
    }
}