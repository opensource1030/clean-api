<?php

namespace WA\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use WA\DataStore\Company\CompanySetting;
use WA\DataStore\Company\CompanySettingTransformer;
use WA\Repositories\Company\CompanySettingInterface;

/**
 * CompanySettings resource.
 *
 * @Resource("companySetting", uri="/companySetting")
 */
class CompanySettingsController extends FilteredApiController
{
    /**
     * @var CompanySettingInterface
     */
    protected $companySetting;

    /**
     * CompanySetting Controller constructor.
     *
     * @param CompanySettingInterface $companySetting
     * @param Request $request
     */
    public function __construct(CompanySettingInterface $companySetting, Request $request)
    {
        parent::__construct($companySetting, $request);
        $this->companySetting = $companySetting;
    }

    /**
     * Update contents of an CompanySetting.
     *
     * @param $id
     *
     * @return \Dingo\Api\Http\Response
     */
    public function store($id, Request $request)
    {
        if ($this->isJsonCorrect($request, 'companysettings')) {
            try {
                $data = $request->all()['data'];
                $data['attributes']['id'] = $id;
                $companySetting = $this->companySetting->update($data['attributes']);

                if ($companySetting == 'notExist') {
                    $error['errors']['CompanySettings'] = Lang::get('messages.NotExistClass', ['class' => 'CompanySettings']);
                    return response()->json($error)->setStatusCode($this->status_codes['notexists']);
                }

                if ($companySetting == 'notSaved') {
                    $error['errors']['CompanySettings'] = Lang::get('messages.NotSavedClass', ['class' => 'CompanySettings']);
                    return response()->json($error)->setStatusCode($this->status_codes['conflict']);
                }

                return $this->response()->item($companySetting, new companySettingTransformer(),
                    ['key' => 'companySettings'])->setStatusCode($this->status_codes['created']);
            } catch (\Exception $e) {
                $error['errors']['CompanySetting'] = Lang::get('messages.NotOptionIncludeClass',
                    ['class' => 'CompanySettings', 'option' => 'updated', 'include' => '']);
                $error['errors']['CompanySettingsMessage'] = $e->getMessage();
            }
        } else {
            $error['errors']['json'] = Lang::get('messages.InvalidJson');
        }

        return response()->json($error)->setStatusCode($this->status_codes['conflict']);
    }

    /**
     * Create a new CompanySettings.
     *
     * @return \Dingo\Api\Http\Response
     */
    public function create(Request $request)
    {
        if ($this->isJsonCorrect($request, 'companysettings')) {
            try {
                $data = $request->all()['data']['attributes'];
                $companySetting = $this->companySetting->create($data);

                return $this->response()->item($companySetting, new companySettingTransformer(),
                    ['key' => 'companysettings'])->setStatusCode($this->status_codes['created']);
            } catch (\Exception $e) {
                $error['errors']['CompanySetting'] = Lang::get('messages.NotOptionIncludeClass',
                    ['class' => 'CompanySettings', 'option' => 'created', 'include' => '']);
                //$error['errors']['Message'] = $e->getMessage();
            }
        } else {
            $error['errors']['json'] = Lang::get('messages.InvalidJson');
        }

        return response()->json($error)->setStatusCode($this->status_codes['conflict']);
    }

    /**
     * Delete a CompanySettings.
     *
     * @param $id
     */
    public function delete($id)
    {
        $companySetting = CompanySetting::find($id);
        if ($companySetting != null) {
            $this->companySetting->deleteById($id);
        } else {
            $error['errors']['delete'] = Lang::get('messages.NotExistClass', ['class' => 'CompanySetting']);
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }

        $companySetting = CompanySetting::find($id);
        if ($companySetting == null) {
            return array("success" => true);
        } else {
            $error['errors']['delete'] = Lang::get('messages.NotDeletedClass', ['class' => 'CompanySetting']);
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }
    }
}
