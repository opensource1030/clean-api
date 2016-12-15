<?php

namespace WA\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use WA\DataStore\DeviceVariation\DeviceVariation;
use WA\DataStore\DeviceVariation\DeviceVariationTransformer;
use WA\Repositories\DeviceVariation\DeviceVariationInterface;

use DB;

/**
 * DeviceVariation resource.
 *
 * @Resource("DeviceVariation", uri="/devicevariations")
 */
class DeviceVariationsController extends FilteredApiController
{
    /**
     * @var DeviceVariationInterface
     */
    protected $deviceVariation;

    /**
     * DeviceVariationsController constructor.
     *
     * @param DeviceVariationInterface $deviceVariation
     * @param Request $request
     */
    public function __construct(DeviceVariationInterface $deviceVariation, Request $request)
    {
        parent::__construct($deviceVariation, $request);
        $this->deviceVariation = $deviceVariation;
    }

    /**
     * Update contents of a DeviceVariation.
     *
     * @param $id
     *
     * @return \Dingo\Api\Http\Response
     */
    public function store($id, Request $request)
    {
        $success = true;
        $dataModifications = array();

        /*
         * Checks if Json has data, data-type & data-attributes.
         */
        if (!$this->isJsonCorrect($request, 'deviceVariations')) {
            $error['errors']['json'] = Lang::get('messages.InvalidJson');
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }

        DB::beginTransaction();

        /*
         * Now we can create the DeviceVariation.
         */
        try {
            $data = $request->all()['data']['attributes'];
            $data['id'] = $id;
            $deviceVariation = $this->deviceVariation->update($data);

            if ($deviceVariation == 'notExist') {
                DB::rollBack();
                $error['errors']['deviceVariation'] = Lang::get('messages.NotExistClass', ['class' => 'DeviceVariation']);
                //$error['errors']['Message'] = $e->getMessage();
                return response()->json($error)->setStatusCode($this->status_codes['notexists']);
            }

            if ($deviceVariation == 'notSaved') {
                DB::rollBack();
                $error['errors']['deviceVariation'] = Lang::get('messages.NotSavedClass', ['class' => 'DeviceVariation']);
                //$error['errors']['Message'] = $e->getMessage();
                return response()->json($error)->setStatusCode($this->status_codes['conflict']);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $success = false;
            $error['errors']['deviceVariations'] = Lang::get('messages.NotOptionIncludeClass',
                ['class' => 'DeviceVariation', 'option' => 'updated', 'include' => '']);
            //$error['errors']['Message'] = $e->getMessage();
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }

        /*
         * Check if Json has relationships to continue or if not and commit + return.
         */
        if (isset($data['relationships'])) {
            $dataRelationships = $data['relationships'];

            if (isset($dataRelationships['modifications'])) {
                if (isset($dataRelationships['modifications']['data'])) {
                    $dataDeviceVariations = $this->parseJsonToArray($dataRelationships['modifications']['data'], 'modifications');
                    try {
                        $deviceVariation->modifications()->sync($dataDeviceVariations);
                    } catch (\Exception $e) {
                        $error['errors']['modifications'] = Lang::get('messages.NotOptionIncludeClass',
                            ['class' => 'DeviceVariation', 'option' => 'updated', 'include' => 'Modifications']);
                        //$error['errors']['ModificationsMessage'] = $e->getMessage();
                    }
                }
            }
            if (isset($dataRelationships['carriers'])) {
                if (isset($dataRelationships['carriers']['data'])) {
                    $dataCarriers = $this->parseJsonToArray($dataRelationships['carriers']['data'], 'carriers');
                    try {
                        $device->carriers()->sync($dataCarriers);
                    } catch (\Exception $e) {
                        $error['errors']['carriers'] = Lang::get('messages.NotOptionIncludeClass',
                            ['class' => 'DeviceVariation', 'option' => 'updated', 'include' => 'Carriers']);
                        //$error['errors']['Message'] = $e->getMessage();
                    }
                }
            }
            if (isset($dataRelationships['devices'])) {
                if (isset($dataRelationships['devices']['data'])) {
                    $dataDevices = $this->parseJsonToArray($dataRelationships['devices']['data'], 'devices');
                    try {
                        $device->devices()->sync($dataDevices);
                    } catch (\Exception $e) {
                        $error['errors']['devices'] = Lang::get('messages.NotOptionIncludeClass',
                            ['class' => 'DeviceVariation', 'option' => 'updated', 'include' => 'devices']);
                        //$error['errors']['Message'] = $e->getMessage();
                    }
                }
            }
            
        }

        if ($success) {
            DB::commit();
            return $this->response()->item($deviceVariation, new DeviceVariationTransformer(),
                ['key' => 'devicevariations'])->setStatusCode($this->status_codes['created']);
        } else {
            DB::rollBack();
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }
    }

    /**
     * Create a new DeviceVariation.
     *
     * @return \Dingo\Api\Http\Response
     */
    public function create(Request $request)
    {
        $success = true;
        $dataModifications = array();

        /*
         * Checks if Json has data, data-type & data-attributes.
         */
        if (!$this->isJsonCorrect($request, 'deviceVariations')) {
            $error['errors']['json'] = Lang::get('messages.InvalidJson');
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }

        DB::beginTransaction();

        /*
         * Now we can create the DeviceVariation.
         */
        try {
            $data = $request->all()['data']['attributes'];
            $deviceVariation = $this->deviceVariation->create($data);
        } catch (\Exception $e) {
            DB::rollBack();
            $error['errors']['deviceVariations'] = Lang::get('messages.NotOptionIncludeClass',
                ['class' => 'DeviceVariation', 'option' => 'created', 'include' => '']);
            //$error['errors']['Message'] = $e->getMessage();
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }

        /*
         * Check if Json has relationships to continue or if not and commit + return.
         */
        if (isset($data['relationships'])) {
            $dataRelationships = $data['relationships'];

            if (isset($dataRelationships['modifications'])) {
                if (isset($dataRelationships['modifications']['data'])) {
                    $dataDeviceValiations = $this->parseJsonToArray($dataRelationships['modifications']['data'], 'modifications');
                    try {
                        $deviceVariation->modifications()->sync($dataDeviceValiations);
                    } catch (\Exception $e) {
                        $error['errors']['modifications'] = Lang::get('messages.NotOptionIncludeClass',
                            ['class' => 'DeviceVariation', 'option' => 'updated', 'include' => 'Modifications']);
                        //$error['errors']['ModificationsMessage'] = $e->getMessage();
                    }
                }
            }
            if (isset($dataRelationships['presets'])) {
                if (isset($dataRelationships['presets']['data'])) {
                    $dataDeviceVariations = $this->parseJsonToArray($dataRelationships['presets']['data'], 'presets');
                    try {
                        $deviceVariation->presets()->sync($dataDeviceVariations);
                    } catch (\Exception $e) {
                        $error['errors']['presets'] = Lang::get('messages.NotOptionIncludeClass',
                            ['class' => 'DeviceVariation', 'option' => 'updated', 'include' => 'Presets']);
                        //$error['errors']['ModificationsMessage'] = $e->getMessage();
                    }
                }
            }
            if (isset($dataRelationships['carriers'])) {
                if (isset($dataRelationships['carriers']['data'])) {
                    $dataCarriers = $this->parseJsonToArray($dataRelationships['carriers']['data'], 'carriers');
                    try {
                        $device->carriers()->sync($dataCarriers);
                    } catch (\Exception $e) {
                        $error['errors']['carriers'] = Lang::get('messages.NotOptionIncludeClass',
                            ['class' => 'DeviceVariation', 'option' => 'updated', 'include' => 'Carriers']);
                        //$error['errors']['Message'] = $e->getMessage();
                    }
                }
            }
            if (isset($dataRelationships['devices'])) {
                if (isset($dataRelationships['devices']['data'])) {
                    $dataDevices = $this->parseJsonToArray($dataRelationships['devices']['data'], 'devices');
                    try {
                        $device->devices()->sync($dataDevices);
                    } catch (\Exception $e) {
                        $error['errors']['devices'] = Lang::get('messages.NotOptionIncludeClass',
                            ['class' => 'DeviceVariation', 'option' => 'updated', 'include' => 'devices']);
                        //$error['errors']['Message'] = $e->getMessage();
                    }
                }
            }
        }

        if ($success) {
            DB::commit();
            return $this->response()->item($deviceVariation, new DeviceVariationTransformer(),
                ['key' => 'devicevariations'])->setStatusCode($this->status_codes['created']);
        } else {
            DB::rollBack();
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }
    }

    /**
     * Delete a DeviceVariation.
     *
     * @param $id
     */
    public function delete($id)
    {
        $deviceVariation = DeviceVariation::find($id);
        if ($deviceVariation != null) {
            $this->deviceVariation->deleteById($id);
        } else {
            $error['errors']['delete'] = Lang::get('messages.NotExistClass', ['class' => 'DeviceVariation']);
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }


        $deviceVariation = DeviceVariation::find($id);
        if ($deviceVariation == null) {
            return array("success" => true);
        } else {
            $error['errors']['delete'] = Lang::get('messages.NotDeletedClass', ['class' => 'DeviceVariation']);
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }
    }
}
