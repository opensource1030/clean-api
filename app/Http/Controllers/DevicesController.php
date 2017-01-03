<?php

namespace WA\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use WA\DataStore\DeviceVariation\DeviceVariation;
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
            //$error['errors']['json'] = Lang::get('messages.InvalidJson');
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
           
           /* try {
                $deviceVariations = DeviceVariation::where('deviceId', $id)->get();
                //$helper = app()->make('WA\Http\Controllers\DeviceVariationsHelperController');
            } catch (\Exception $e) {
                $error['errors']['devicevariations'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'Device', 'option' => 'updated', 'include' => 'DeviceVariations']);
                //$error['errors']['Message'] = $e->getMessage();
                return response()->json($error)->setStatusCode($this->status_codes['conflict']);
            }*/

            if (isset($dataRelationships['devicevariations'])) {
                if (isset($dataRelationships['devicevariations']['data'])) {

                    if ($success) {
                        try {    
                            $deviceVar = DeviceVariation::where('deviceId', $id)->get();
                                                   
                            $helper = app()->make('WA\Http\Controllers\DeviceVariationsHelperController');

                            
        
                            $this->deleteNotRequested($dataRelationships['devicevariations']['data'], $deviceVar, $helper, 'devicevariations');                       
                                //$check = $this->checkIfDeviceVariationsRowIsCorrect($deviceVariations, $dataModifications);
                                //if ($check['bool']) {
                                    
                                    $success=$helper->store($dataRelationships['devicevariations'],$device->id);
                                    if (!$success){ 
                                        $error['errors']['devicevariations'] = Lang::get('messages.NotOptionIncludeClass',
                                        ['class' => 'Device', 'option' => 'updated', 'include' => 'DeviceVariations']);
                                        //$error['errors']['Message'] = $e->getMessage();
                                    }
                                    /*if (isset($deviceVariations['id'])) {
                                        if ($deviceVariations['id'] == 0) {
                                            $helper->create($deviceVariations);
                                        } else {
                                            if ($deviceVariations['id'] > 0) {
                                                $helper->update($deviceVariations);
                                            } else {
                                                $success = false;
                                                $error['errors']['devicevariations'] = 'the Device Variation has an incorrect id';
                                            }
                                        }
                                    } else {
                                        $success = false;
                                        $error['errors']['devicevariations'] = 'the Device Variation has no id';
                                    }*/

                               /* } else {
                                    $success = false;
                                    $error['errors']['devicevariations'] = Lang::get('messages.NotOptionIncludeClass',
                                        ['class' => 'Device', 'option' => 'updated', 'include' => 'Devicevariations']);
                                    //$error['errors']['Check'] = $check['error'];
                                    //$error['errors']['IdError'] = $check['id'];
                                    //$error['errors']['Message'] = 'Any price rows are not correct and no references provided relationships.';
                                }*/
                            
                        } catch (\Exception $e) {
                            $success = false;
                            $error['errors']['devicevariations'] = Lang::get('messages.NotOptionIncludeClass',
                                ['class' => 'Device', 'option' => 'updated', 'include' => 'DeviceVariations']);
                            //$error['errors']['Message'] = $e->getMessage();
                        }
                    } else {
                        $success = false;
                        $error['errors']['devicevariations'] = Lang::get('messages.NotIncludeExistsOptionClass',
                            ['class' => 'Device', 'option' => 'updated', 'include' => 'Devicevariations']);
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
            $error['errors']['Message'] = $e->getMessage();
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
                    //$data = $dataRelationships['devicevariations']['data'];



                    if ($success) {
                        try {
                            $helper = app()->make('WA\Http\Controllers\DeviceVariationsHelperController');

                            //$data = $this->deleteRepeat($data);
                            

                            //foreach ($data as $deviceVariations) {
                                //$check = $this->checkIfDeviceVariationsRowIsCorrect($deviceVariations, $device);
                               // if ($check['bool']) {
                                    //$data['attributes']['deviceId'] = $device->id;
                                    $helper->create($dataRelationships['devicevariations'], $device->id);
                               /* } else {
                                    $success = false;
                                    $error['errors']['devicevariations'] = Lang::get('messages.NotOptionIncludeClass',
                                        ['class' => 'Device', 'option' => 'created', 'include' => 'DeviceVariations']);
                                    //$error['errors']['Check'] = $check['error'];
                                    //$error['errors']['IdError'] = $check['id'];
                                    //$error['errors']['Message'] = 'Any devicevariations rows are not correct and no references provided relationships.';
                                }*/
                            
                        } catch (\Exception $e) {

                            $success = false;
                            $errors = 'Device Variation not created.';
                           //$error['errors']['devicevariations'] = Lang::get('messages.NotOptionIncludeClass',
                                //['class' => 'Device', 'option' => 'created', 'include' => 'DeviceVariations']);
                            $error['errors']['Message'] = $e->getMessage();
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

                    if (isset($dataAux[$k]['carrierId']) && isset($data[$j]['carrierId'])) {
                        if ($dataAux[$k]['carrierId'] <> $data[$j]['carrierId']) {
                            $esIgual = $esIgual && false;
                        }    
                    }
                    if (isset($dataAux[$k]['deviceId']) && isset($data[$j]['deviceId'])) {
                        if ($dataAux[$k]['deviceId'] <> $data[$j]['deviceId']) {
                            $esIgual = $esIgual && false;
                        }
                    }
                    if (isset($dataAux[$k]['companyId']) && isset($data[$j]['companyId'])) {
                        if ($dataAux[$k]['companyId'] <> $data[$j]['companyId']) {
                            $esIgual = $esIgual && false;
                        }
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
        if ($device == null) {
            return array("success" => true);
        } else {
            $error['errors']['delete'] = Lang::get('messages.NotDeletedClass', ['class' => 'Device']);
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }
    }
}
