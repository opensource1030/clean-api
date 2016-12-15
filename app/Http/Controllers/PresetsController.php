<?php

namespace WA\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use WA\DataStore\Category\Preset;
use WA\DataStore\Category\PresetTransformer;
use WA\Repositories\Category\PresetInterface;

/**
 * Preset resource.
 *
 * @Resource("preset", uri="/preset")
 */
class PresetsController extends FilteredApiController
{
    /**
     * @var PresetInterface
     */
    protected $preset;

    /**
     * PresetsController constructor.
     *
     * @param PresetInterface $preset
     * @param Request $request
     */
    public function __construct(PresetInterface $preset, Request $request)
    {
        parent::__construct($preset, $request);
        $this->preset = $preset;
    }

    /**
     * Update contents of a Preset.
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
        if (!$this->isJsonCorrect($request, 'presets')) {
            $error['errors']['json'] = Lang::get('messages.InvalidJson');

            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }

        DB::beginTransaction();

        try {
            $data = $request->all()['data']['attributes'];
            $data['id'] = $id;
            $preset = $this->preset->update($data);

            if ($preset == 'notExist') {
                DB::rollBack();
                $error['errors']['preset'] = Lang::get('messages.NotExistClass', ['class' => 'Preset']);
                //$error['errors']['Message'] = $e->getMessage();
                return response()->json($error)->setStatusCode($this->status_codes['notexists']);
            }

            if ($preset == 'notSaved') {
                DB::rollBack();
                $error['errors']['preset'] = Lang::get('messages.NotSavedClass', ['class' => 'Preset']);
                //$error['errors']['Message'] = $e->getMessage();
                return response()->json($error)->setStatusCode($this->status_codes['conflict']);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $error['errors']['preset'] = Lang::get('messages.NotOptionIncludeClass',
                ['class' => 'Preset', 'option' => 'updated', 'include' => '']);
            //$error['errors']['Message'] = $e->getMessage();
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }

        if (isset($data['relationships'])) {
            if (isset($data['relationships']['company'])) {
                if (isset($data['relationships']['company']['data'])) {
                    try {
                        $dataCompany = $this->parseJsonToArray($data['relationships']['company']['data'], 'company');
                        $preset->company()->sync($dataCompany);
                    } catch (\Exception $e) {
                        $success = false;
                        $error['errors']['company'] = Lang::get('messages.NotOptionIncludeClass',
                            ['class' => 'Preset', 'option' => 'created', 'include' => 'Company']);
                        //$error['errors']['Message'] = $e->getMessage();
                    }
                }
            }

            if (isset($data['relationships']['deviceVariations'])) {
                if (isset($data['relationships']['deviceVariations']['data'])) {
                    try {
                        $dataDeviceVariations = $this->parseJsonToArray($data['relationships']['deviceVariations']['data'], 'deviceVariations');
                        $preset->deviceVariations()->sync($dataDeviceVariations);
                    } catch (\Exception $e) {
                        $success = false;
                        $error['errors']['deviceVariations'] = Lang::get('messages.NotOptionIncludeClass',
                            ['class' => 'Preset', 'option' => 'created', 'include' => 'DeviceVariations']);
                        //$error['errors']['Message'] = $e->getMessage();
                    }
                }
            }
        }

        if ($success) {
            DB::commit();

            return $this->response()->item($preset, new PresetTransformer(),
                ['key' => 'presets'])->setStatusCode($this->status_codes['created']);
        } else {
            DB::rollBack();

            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }
    }

    /**
     * Create a new Preset.
     *
     * @return \Dingo\Api\Http\Response
     */
    public function create(Request $request)
    {
        $success = true;
        /*
         * Checks if Json has data, data-type & data-attributes.
         */
        if (!$this->isJsonCorrect($request, 'presets')) {
            $error['errors']['json'] = Lang::get('messages.InvalidJson');

            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }

        DB::beginTransaction();

        try {
            $data = $request->all()['data']['attributes'];
            $preset = $this->preset->create($data);
        } catch (\Exception $e) {
            DB::rollBack();
            $error['errors']['preset'] = Lang::get('messages.NotOptionIncludeClass',
                ['class' => 'Preset', 'option' => 'created', 'include' => '']);
            //$error['errors']['Message'] = $e->getMessage();
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }

        if (isset($data['relationships'])) {
            if (isset($data['relationships']['company'])) {
                if (isset($data['relationships']['company']['data'])) {
                    try {
                        $dataCompany = $this->parseJsonToArray($data['relationships']['company']['data'], 'company');
                        $preset->company()->sync($dataCompany);
                    } catch (\Exception $e) {
                        $success = false;
                        $error['errors']['company'] = Lang::get('messages.NotOptionIncludeClass',
                            ['class' => 'Preset', 'option' => 'created', 'include' => 'Company']);
                        //$error['errors']['Message'] = $e->getMessage();
                    }
                }
            }

            if (isset($data['relationships']['deviceVariations'])) {
                if (isset($data['relationships']['deviceVariations']['data'])) {
                    try {
                        $datadeviceVariations = $this->parseJsonToArray($data['relationships']['deviceVariations']['data'], 'deviceVariations');
                        $preset->deviceVariations()->sync($datadeviceVariations);
                    } catch (\Exception $e) {
                        $success = false;
                        $error['errors']['deviceVariations'] = Lang::get('messages.NotOptionIncludeClass',
                            ['class' => 'Preset', 'option' => 'created', 'include' => 'deviceVariations']);
                        //$error['errors']['Message'] = $e->getMessage();
                    }
                }
            }
        }

        if ($success) {
            DB::commit();

            return $this->response()->item($preset, new PresetTransformer(),
                ['key' => 'presets'])->setStatusCode($this->status_codes['created']);
        } else {
            DB::rollBack();

            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }
    }

    /**
     * Delete a Preset.
     *
     * @param $id
     */
    public function delete($id)
    {
        $preset = Preset::find($id);
        if ($preset != null) {
            $this->preset->deleteById($id);
        } else {
            $error['errors']['delete'] = Lang::get('messages.NotExistClass', ['class' => 'Preset']);
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }

        $preset = Preset::find($id);
        if ($preset == null) {
            return array("success" => true);
        } else {
            $error['errors']['delete'] = Lang::get('messages.NotDeletedClass', ['class' => 'Preset']);
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }
    }
}
