<?php

namespace WA\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use WA\DataStore\Price\Price;
use WA\DataStore\Device\Device;
use WA\DataStore\Device\DeviceTransformer;
use WA\Repositories\Device\DeviceInterface;

/**
 * Devices resource.
 *
 * @Resource("Devices", uri="/devices")
 */
class DevicesController extends FilteredApiController
{
    /**
     * @var DeviceInterface
     */
    protected $device;

    /**
     * Package Controller constructor
     *
     * @param DeviceInterface $device
     * @param Request $request
     */
    public function __construct(DeviceInterface $device, Request $request)
    {
        parent::__construct($device, $request);
        $this->device = $device;
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
        $dataImages = $dataModifications = array();

        /*
         * Checks if Json has data, data-type & data-attributes.
         */
        if (!$this->isJsonCorrect($request, 'devices')) {
            $error['errors']['json'] = Lang::get('messages.InvalidJson');
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }

        DB::beginTransaction();

        /*
         * Now we can update the Device.
         */
        try {
            $data = $request->all()['data'];
            $data['attributes']['id'] = $id;
            $device = $this->device->update($data['attributes']);

            if ($device == 'notExist') {
                DB::rollBack();
                $error['errors']['device'] = Lang::get('messages.NotExistClass', ['class' => 'Device']);
                //$error['errors']['Message'] = $e->getMessage();
                return response()->json($error)->setStatusCode($this->status_codes['notexists']);
            }

            if ($device == 'notSaved') {
                DB::rollBack();
                $error['errors']['device'] = Lang::get('messages.NotSavedClass', ['class' => 'Device']);
                //$error['errors']['Message'] = $e->getMessage();
                return response()->json($error)->setStatusCode($this->status_codes['conflict']);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            $error['errors']['devices'] = Lang::get('messages.NotOptionIncludeClass',
                ['class' => 'Device', 'option' => 'updated', 'include' => '']);
            //$error['errors']['Message'] = $e->getMessage();
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
                    } catch (\Exception $e) {
                        $error['errors']['images'] = Lang::get('messages.NotOptionIncludeClass',
                            ['class' => 'Device', 'option' => 'updated', 'include' => 'Images']);
                        //$error['errors']['Message'] = $e->getMessage();
                    }
                }
            }

            if (isset($dataRelationships['assets'])) {
                if (isset($dataRelationships['assets']['data'])) {
                    $dataAssets = $this->parseJsonToArray($dataRelationships['assets']['data'], 'assets');
                    try {
                        $device->assets()->sync($dataAssets);
                    } catch (\Exception $e) {
                        $error['errors']['assets'] = Lang::get('messages.NotOptionIncludeClass',
                            ['class' => 'Device', 'option' => 'updated', 'include' => 'Assets']);
                        //$error['errors']['Message'] = $e->getMessage();
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
                        $error['errors']['modifications'] = Lang::get('messages.NotOptionIncludeClass',
                            ['class' => 'Device', 'option' => 'updated', 'include' => 'Modifications']);
                        //$error['errors']['Message'] = $e->getMessage();
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
                        $error['errors']['carriers'] = Lang::get('messages.NotOptionIncludeClass',
                            ['class' => 'Device', 'option' => 'updated', 'include' => 'Carriers']);
                        //$error['errors']['Message'] = $e->getMessage();
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
                        $error['errors']['companies'] = Lang::get('messages.NotOptionIncludeClass',
                            ['class' => 'Device', 'option' => 'updated', 'include' => 'Companies']);
                        //$error['errors']['Message'] = $e->getMessage();
                    }
                }
            }

            try {
                $deviceVariations = DeviceVariation::where('deviceId', $id)->get();
                $interface = app()->make('WA\Repositories\DeviceVariation\DeviceVariationInterface');
            } catch (\Exception $e) {
                $error['errors']['devicevariations'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'Device', 'option' => 'updated', 'include' => 'DeviceVariations']);
                $error['errors']['Message'] = $e->getMessage();
                return response()->json($error)->setStatusCode($this->status_codes['conflict']);
            }

            if (isset($dataRelationships['devicevariations'])) {
                if (isset($dataRelationships['devicevariations']['data'])) {
                    $data = $dataRelationships['devicevariations']['data'];

                    if ($success) {
                        try {                           

                            $data = $this->deleteRepeat($data);

                            $this->deleteNotRequested($data, $deviceVariations, $interface, 'devicevariations');

                            foreach ($data as $deviceVariations) {
                                $check = $this->checkIfDeviceVariationsRowIsCorrect($deviceVariations, $dataModifications);
                                if ($check['bool']) {
                                    $deviceVariations['deviceId'] = $device->id;

                                    if (isset($deviceVariations['id'])) {
                                        if ($deviceVariations['id'] == 0) {
                                            $interface->create($deviceVariations);
                                        } else {
                                            if ($deviceVariations['id'] > 0) {
                                                $interface->update($deviceVariations);
                                            } else {
                                                $success = false;
                                                $error['errors']['devicevariations'] = 'the Device Variation has an incorrect id';
                                            }
                                        }
                                    } else {
                                        $success = false;
                                        $error['errors']['devicevariations'] = 'the Device Variation has no id';
                                    }

                                } else {
                                    $success = false;
                                    $error['errors']['devicevariations'] = Lang::get('messages.NotOptionIncludeClass',
                                        ['class' => 'Device', 'option' => 'updated', 'include' => 'DeviceVariations']);
                                    //$error['errors']['Check'] = $check['error'];
                                    //$error['errors']['IdError'] = $check['id'];
                                    //$error['errors']['Message'] = 'Any price rows are not correct and no references provided relationships.';
                                }
                            }
                        } catch (\Exception $e) {
                            $success = false;
                            $error['errors']['devicevariations'] = Lang::get('messages.NotOptionIncludeClass',
                                ['class' => 'Device', 'option' => 'updated', 'include' => 'DeviceVariations']);
                            //$error['errors']['Message'] = $e->getMessage();
                        }
                    } else {
                        $success = false;
                        $error['errors']['devicevariations'] = Lang::get('messages.NotIncludeExistsOptionClass',
                            ['class' => 'Device', 'option' => 'updated', 'include' => 'DeviceVariations']);
                        //$error['errors']['Message'] = $e->getMessage();
                    }
                }
            }
        }

        if ($success) {
            DB::commit();
            return $this->response()->item($device, new DeviceTransformer(),
                ['key' => 'devices'])->setStatusCode($this->status_codes['created']);
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
        $dataImages =  $dataModifications = array();

        /*
         * Checks if Json has data, data-type & data-attributes.
         */
        if (!$this->isJsonCorrect($request, 'devices')) {
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
            $error['errors']['devices'] = Lang::get('messages.NotOptionIncludeClass',
                ['class' => 'Device', 'option' => 'created', 'include' => '']);
            //$error['errors']['Message'] = $e->getMessage();
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
                    } catch (\Exception $e) {
                        $error['errors']['images'] = Lang::get('messages.NotOptionIncludeClass',
                            ['class' => 'Device', 'option' => 'created', 'include' => 'Images']);
                        //$error['errors']['Message'] = $e->getMessage();
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
                        $error['errors']['modifications'] = Lang::get('messages.NotOptionIncludeClass',
                            ['class' => 'Device', 'option' => 'created', 'include' => 'Modifications']);
                        //$error['errors']['Message'] = $e->getMessage();
                    }
                }
            }

            if (isset($dataRelationships['devicevariations'])) {
                if (isset($dataRelationships['devicevariations']['data'])) {
                    $data = $dataRelationships['devicevariations']['data'];

                    if ($success) {
                        try {
                            $interface = app()->make('WA\Repositories\DeviceVariation\DeviceVariationInterface');

                            $data = $this->deleteRepeat($data);

                            foreach ($data as $deviceVariations) {
                                $check = $this->checkIfDeviceVariationsRowIsCorrect($deviceVariations, $device);
                                if ($check['bool']) {
                                    $deviceVariations['deviceId'] = $device->id;
                                    $interface->create($deviceVariations);
                                } else {
                                    $success = false;
                                    $error['errors']['devicevariations'] = Lang::get('messages.NotOptionIncludeClass',
                                        ['class' => 'Device', 'option' => 'created', 'include' => 'DeviceVariations']);
                                    //$error['errors']['Check'] = $check['error'];
                                    //$error['errors']['IdError'] = $check['id'];
                                    //$error['errors']['Message'] = 'Any devicevariations rows are not correct and no references provided relationships.';
                                }
                            }
                        } catch (\Exception $e) {
                            $success = false;
                            $error['errors']['devicevariations'] = Lang::get('messages.NotOptionIncludeClass',
                                ['class' => 'Device', 'option' => 'created', 'include' => 'DeviceVariations']);
                            //$error['errors']['Message'] = $e->getMessage();
                        }
                    } else {
                        $success = false;
                        $error['errors']['devicevariations'] = Lang::get('messages.NotIncludeExistsOptionClass',
                            ['class' => 'Device', 'option' => 'created', 'include' => 'DeviceVariations']);
                        //$error['errors']['Message'] = $e->getMessage();
                    }
                }
            }
        }

        if ($success) {
            DB::commit();
            return $this->response()->item($device, new DeviceTransformer(),
                ['key' => 'devices'])->setStatusCode($this->status_codes['created']);
        } else {
            DB::rollBack();
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }
    }

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
                    if ($dataAux[$k]['deviceId'] <> $data[$j]['deviceId']) {
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

    private function checkIfDeviceVariationsRowIsCorrect($deviceVariations, $deviceId)
    {   $modInterface = app()->make('WA\Repositories\Device\DeviceInterface');

        if (isset($deviceVariations['deviceId'])) {

            $device = $modInterface->byId($mod);
                $reflectorResponse = new \ReflectionClass($device);
                $classResponse = $reflectorResponse->getProperty('attributes');
                $classResponse->setAccessible(true);
                $dataResponse = $classResponse->getValue($device);
            if ($deviceVariations['deviceId'] == $dataResponse['id']) {
                    return array("bool" => true, "error" => "No Error", "id" => 0);
            }
            else{
                return array("bool" => false, "error" => "Id Not Found", "id" => $deviceVariations['deviceId']);
            }
        }

        
        /*$modInterface = app()->make('WA\Repositories\Modification\ModificationInterface');
        $existsCapacity = false;
        if (isset($deviceVariations['deviceId'])) {

            foreach ($modifications as $mod) {

                $modification = $modInterface->byId($mod);
                $reflectorResponse = new \ReflectionClass($modification);
                $classResponse = $reflectorResponse->getProperty('attributes');
                $classResponse->setAccessible(true);
                $dataResponse = $classResponse->getValue($modification);

                if ($deviceVariations['deviceId'] == $dataResponse['id']) {
                    if ($dataResponse['modType'] == 'capacity') {
                        $existsCapacity = true;
                    }
                }
            }

            if (!$existsCapacity) {
                return array("bool" => false, "error" => "Capacity Not Found", "id" => $deviceVariations['deviceId']);
            }
        }


        $existsStyle = false;
        if (isset($deviceVariations['styleId'])) {

            foreach ($modifications as $mod) {

                $modification = $modInterface->byId($mod);
                $reflectorResponse = new \ReflectionClass($modification);
                $classResponse = $reflectorResponse->getProperty('attributes');
                $classResponse->setAccessible(true);
                $dataResponse = $classResponse->getValue($modification);

                if ($deviceVariations['styleId'] == $dataResponse['id']) {
                    if ($dataResponse['modType'] == 'style') {
                        $existsStyle = true;
                    }
                }
            }

            if (!$existsStyle) {
                return array("bool" => false, "error" => "Style Not Found", "id" => $deviceVariations['styleId']);
            }
        }

        return array("bool" => true, "error" => "No Error", "id" => 0);*/
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

    /**
     * Create a new device
     *
     * @return \Dingo\Api\Http\Response
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
        if ($device == null) {
            return array("success" => true);
        } else {
            $error['errors']['delete'] = Lang::get('messages.NotDeletedClass', ['class' => 'Device']);
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }
    }
}
