<?php

namespace WA\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use WA\DataStore\GlobalSetting\GlobalSetting;
use WA\DataStore\GlobalSetting\GlobalSettingTransformer;
use WA\Repositories\GlobalSetting\GlobalSettingInterface;

use DB;

/**
 * GlobalSettings resource.
 *
 * @Resource("GlobalSetting", uri="/globalSetting")
 */
class GlobalSettingsController extends FilteredApiController
{
    /**
     * @var GlobalSettingInterface
     */
    protected $globalSetting;

    /**
     * GlobalSetting Controller constructor.
     *
     * @param GlobalSettingInterface $globalSetting
     * @param Request $request
     */
    public function __construct(GlobalSettingInterface $globalSetting, Request $request)
    {
        parent::__construct($globalSetting, $request);
        $this->globalSetting = $globalSetting;
    }

    /**
     * Update contents of an globalSetting.
     *
     * @param $id
     *
     * @return \Dingo\Api\Http\Response
     */
    public function store($id, Request $request)
    {
        $success = true;

        /*
         * Checks if Json has data, data-type & data-attributes.
         */
        if (!$this->isJsonCorrect($request, 'globalsettings')) {
            $error['errors']['json'] = Lang::get('messages.InvalidJson');

            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }

        DB::beginTransaction();

        try {
            $data = $request->all()['data'];
            $data['attributes']['id'] = $id;
            $globalSetting = $this->globalSetting->update($data['attributes']);

            if ($globalSetting == 'notExist') {
                DB::rollBack();
                $error['errors']['globalSetting'] = Lang::get('messages.NotExistClass', ['class' => 'GlobalSetting']);
                //$error['errors']['Message'] = $e->getMessage();
                return response()->json($error)->setStatusCode($this->status_codes['notexists']);
            }

            if ($globalSetting == 'notSaved') {
                DB::rollBack();
                $error['errors']['globalSetting'] = Lang::get('messages.NotSavedClass', ['class' => 'GlobalSetting']);
                //$error['errors']['Message'] = $e->getMessage();
                return response()->json($error)->setStatusCode($this->status_codes['conflict']);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $error['errors']['globalSetting'] = Lang::get('messages.NotOptionIncludeClass',
                ['class' => 'GlobalSetting', 'option' => 'updated', 'include' => '']);
            //$error['errors']['Message'] = $e->getMessage();
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }

        /*
         * Check if Json has relationships to continue or if not and commit + return.
         */
        if (isset($data['relationships'])) {
            $dataRelationships = $data['relationships'];

            if (isset($dataRelationships['globalsettings'])) {
                if (isset($dataRelationships['globalsettings']['data'])) {
                    $globalSettings = $dataRelationships['globalsettings']['data'];
                    
                    try {
                        $globalSettingsValueInterface = app()->make('WA\Repositories\GlobalSettingValue\GlobalSettingValueInterface');
                        $globalSettingsValues = GlobalSettingsValue::where('globalSettingId', $id)->get();
                        
                        $this->deleteNotRequested($globalSettings, $globalSettingsValues, $globalSettingsValueInterface, 'globalsettingsvalues');

                        foreach ($globalSettings as $item) {
                            $item['globalSettingsId'] = $id;

                            if (isset($item['id'])) {
                                if ($item['id'] == 0) {
                                    $globalSettingsValueInterface->create($item);
                                } else {
                                    if ($item['id'] > 0) {
                                        $globalSettingsValueInterface->update($item);
                                    } else {
                                        $success = false;
                                        $error['errors']['items'] = 'the GlobalSettings has an incorrect id';
                                    }
                                }
                            } else {
                                $success = false;
                                $error['errors']['globalsettings'] = 'the GlobalSetting has no id';
                            }
                        }

                        if (!$success){
                            $error['errors']['globalsettings'] = Lang::get('messages.NotOptionIncludeClass',['class' => 'GlobalSetting', 'option' => 'updated', 'include' => 'GlobalSettingsValues']);
                        }

                    } catch (\Exception $e) {
                        $success = false;
                        $error['errors']['globalsettings'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'GlobalSetting', 'option' => 'updated', 'include' => 'GlobalSettingsValues']);
                        //$error['errors']['Message'] = $e->getMessage();
                    }
                }
            }
        }

        if ($success) {
            DB::commit();
            return $this->response()->item($globalSetting, new GlobalSettingTransformer(),
                ['key' => 'companies'])->setStatusCode($this->status_codes['created']);
        } else {
            DB::rollBack();
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }
    }

    /**
     * Create a new globalSettings.
     *
     * @return \Dingo\Api\Http\Response
     */
    public function create(Request $request)
    {
        $success = true;
       
        if (!$this->isJsonCorrect($request, 'globalsettings')) {
            $error['errors']['json'] = Lang::get('messages.InvalidJson');
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }
        else{
            $data = $request->all()['data'];
        }

        DB::beginTransaction();

        try {
            $globalSetting = $this->globalSetting->create($data['attributes']);
        } catch (\Exception $e) {
            DB::rollBack();
            $error['errors']['globalSetting'] = Lang::get('messages.NotOptionIncludeClass',
                ['class' => 'GlobalSetting', 'option' => 'created', 'include' => '']);
            //$error['errors']['Message'] = $e->getMessage();
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }

        /*
         * Check if Json has relationships to continue or if not and commit + return.
         */
        if (isset($data['relationships']) && $success) {
            $dataRelationships = $data['relationships'];

            if (isset($dataRelationships['globalsettingsvalues'])) {
                if (isset($dataRelationships['globalsettingsvalues']['data'])) {

                    try {

                        $globalSettingsValueInterface = app()->make('WA\Repositories\GlobalSettingsValue\GlobalSettingsValueInterface');

                        $globalSettingsValueArray = [];
                        foreach ($dataRelationships['globalsettingsvalues']['data'] as $gsv) {
                            $globalSettingsValue = $globalSettingsValueInterface->create($gsv);
                            if (!$globalSettingsValue) {
                                $success = false;
                                $error['errors']['globalsettingsvalues'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'GlobalSettingsValue', 'option' => 'updated', 'include' => '']);
                            } else {
                                array_push($globalSettingsValueArray, $globalSettingsValue->id);    
                            }
                            
                        }
                        if ($success) {
                            $globalSetting->globalsettingsvalues()->sync($globalSettingsValueArray);
                        }

                    } catch (\Exception $e) {
                        $success = false;
                        $error['errors']['udls'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'Udl', 'option' => 'created', 'include' => '']);
                        //$error['errors']['Message'] = $e->getMessage();
                    }
                }
            }
        }

        if($success){
            DB::commit();
            return $this->response()->item($globalSetting, new GlobalSettingTransformer(),
                ['key' => 'globalsettings'])->setStatusCode($this->status_codes['created']);
        } else {
            DB::rollBack();
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }
    }

    /**
     * Delete a globalSettings.
     *
     * @param $id
     */
    public function delete($id)
    {
        $globalSetting = globalSetting::find($id);
        if ($globalSetting != null) {
            $this->globalSetting->deleteById($id);
        } else {
            $error['errors']['delete'] = Lang::get('messages.NotExistClass', ['class' => 'GlobalSetting']);
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }

        $globalSetting = globalSetting::find($id);
        if ($globalSetting == null) {
            return array("success" => true);
        } else {
            $error['errors']['delete'] = Lang::get('messages.NotDeletedClass', ['class' => 'GlobalSetting']);
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }
    }
}
