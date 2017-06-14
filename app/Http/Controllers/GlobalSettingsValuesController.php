<?php

namespace WA\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use WA\DataStore\GlobalSettingValue\GlobalSettingValue;
use WA\DataStore\GlobalSettingValue\GlobalSettingValueTransformer;
use WA\Repositories\GlobalSettingValue\GlobalSettingValueInterface;

/**
 * GlobalSettingValues resource.
 *
 * @Resource("GlobalSettingValue", uri="/globalSettingValue")
 */
class GlobalSettingValuesController extends FilteredApiController
{
    /**
     * @var GlobalSettingValueInterface
     */
    protected $globalSettingValue;

    /**
     * GlobalSettingValue Controller constructor.
     *
     * @param GlobalSettingValueInterface $globalSettingValue
     * @param Request $request
     */
    public function __construct(GlobalSettingValueInterface $globalSettingValue, Request $request)
    {
        parent::__construct($globalSettingValue, $request);
        $this->globalSettingValue = $globalSettingValue;
    }

    /**
     * Update contents of an globalSettingValue.
     *
     * @param $id
     *
     * @return \Dingo\Api\Http\Response
     */
    public function store($id, Request $request)
    {
        if ($this->isJsonCorrect($request, 'globalsettingValues')) {
            try {
                $data = $request->all()['data'];
                $data['attributes']['id'] = $id;
                $globalSettingValue = $this->globalSettingValue->update($data['attributes']);

                if ($globalSettingValue == 'notExist') {
                    $error['errors']['globalSettingValues'] = Lang::get('messages.NotExistClass', ['class' => 'GlobalSettingValue']);
                    return response()->json($error)->setStatusCode($this->status_codes['notexists']);
                }

                if ($globalSettingValue == 'notSaved') {
                    $error['errors']['globalSettingValues'] = Lang::get('messages.NotSavedClass', ['class' => 'GlobalSettingValue']);
                    return response()->json($error)->setStatusCode($this->status_codes['conflict']);
                }

                return $this->response()->item($globalSettingValue, new GlobalSettingValueTransformer(),
                    ['key' => 'globalSettingValues'])->setStatusCode($this->status_codes['created']);
            } catch (\Exception $e) {
                $error['errors']['globalSettingValue'] = Lang::get('messages.NotOptionIncludeClass',
                    ['class' => 'GlobalSettingValue', 'option' => 'updated', 'include' => '']);
                $error['errors']['globalSettingValuesMessage'] = $e->getMessage();
            }
        } else {
            $error['errors']['json'] = Lang::get('messages.InvalidJson');
        }

        return response()->json($error)->setStatusCode($this->status_codes['conflict']);
    }

    /**
     * Create a new globalSettingValues.
     *
     * @return \Dingo\Api\Http\Response
     */
    public function create(Request $request)
    {
        if ($this->isJsonCorrect($request, 'globalsettingValues')) {
            try {
                $data = $request->all()['data']['attributes'];
                $globalSettingValue = $this->globalSettingValue->create($data);

                return $this->response()->item($globalSettingValue, new GlobalSettingValueTransformer(),
                    ['key' => 'globalsettingValues'])->setStatusCode($this->status_codes['created']);
            } catch (\Exception $e) {
                $error['errors']['globalSettingValue'] = Lang::get('messages.NotOptionIncludeClass',
                    ['class' => 'GlobalSettingValue', 'option' => 'created', 'include' => '']);
                //$error['errors']['Message'] = $e->getMessage();
            }
        } else {
            $error['errors']['json'] = Lang::get('messages.InvalidJson');
        }

        return response()->json($error)->setStatusCode($this->status_codes['conflict']);
    }

    /**
     * Delete a globalSettingValues.
     *
     * @param $id
     */
    public function delete($id)
    {
        $globalSettingValue = globalSettingValue::find($id);
        if ($globalSettingValue != null) {
            $this->globalSettingValue->deleteById($id);
        } else {
            $error['errors']['delete'] = Lang::get('messages.NotExistClass', ['class' => 'GlobalSettingValue']);
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }

        $globalSettingValue = globalSettingValue::find($id);
        if ($globalSettingValue == null) {
            return array("success" => true);
        } else {
            $error['errors']['delete'] = Lang::get('messages.NotDeletedClass', ['class' => 'GlobalSettingValue']);
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }
    }
}
