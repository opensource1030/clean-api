<?php

namespace WA\Http\Controllers;

use Illuminate\Http\Request;
use WA\DataStore\Category\Preset;
use WA\DataStore\Category\PresetTransformer;
use WA\Repositories\Category\PresetInterface;
use DB;
use Illuminate\Support\Facades\Lang;

/**
 * Preset resource.
 *
 * @Resource("preset", uri="/preset")
 */
class PresetsController extends ApiController
{
    /**
     * @var PresetInterface
     */
    protected $preset;

    /**
     * Preset Controller constructor.
     *
     * @param PresetInterface $preset
     */
    public function __construct(PresetInterface $preset)
    {
        $this->preset = $preset;
    }

    /**
     * Show all Preset.
     *
     * Get a payload of all Preset
     */
    public function index(Request $request)
    {
        $criteria = $this->getRequestCriteria();
        $this->preset->setCriteria($criteria);
        $preset = $this->preset->byPage();

        if (!$this->includesAreCorrect($request, new PresetTransformer())) {
            $error['errors']['getIncludes'] = Lang::get('messages.NotExistInclude');

            return response()->json($error)->setStatusCode($this->status_codes['badrequest']);
        }

        $response = $this->response()->withPaginator($preset, new PresetTransformer(), ['key' => 'preset']);
        $response = $this->applyMeta($response);

        return $response;
    }

    /**
     * Show a single Preset.
     *
     * Get a payload of a single Preset
     *
     * @Get("/{id}")
     */
    public function show($id, Request $request)
    {
        $criteria = $this->getRequestCriteria();
        $this->preset->setCriteria($criteria);
        $preset = Preset::find($id);

        if ($preset == null) {
            $error['errors']['get'] = Lang::get('messages.NotExistClass', ['class' => 'Preset']);
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }

        if (!$this->includesAreCorrect($request, new PresetTransformer())) {
            $error['errors']['getincludes'] = Lang::get('messages.NotExistInclude');
            return response()->json($error)->setStatusCode($this->status_codes['badrequest']);
        }

        return $this->response()->item($preset, new PresetTransformer(),
            ['key' => 'presets'])->setStatusCode($this->status_codes['created']);
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
            $error['errors']['preset'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'Preset', 'option' => 'updated', 'include' => '']);
            //$error['errors']['Message'] = $e->getMessage();
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }

        if (isset($data['relationships'])) {
            if (isset($data['relationships']['images'])) {
                if (isset($data['relationships']['images']['data'])) {
                    try {
                        $dataImages = $this->parseJsonToArray($data['relationships']['images']['data'], 'images');
                        $preset->images()->sync($dataImages);
                    } catch (\Exception $e) {
                        $success = false;
                        $error['errors']['images'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'Preset', 'option' => 'created', 'include' => 'Images']);
                        //$error['errors']['Message'] = $e->getMessage();
                    }
                }
            }

            if (isset($data['relationships']['devices'])) {
                if (isset($data['relationships']['devices']['data'])) {
                    try {
                        $dataDevices = $this->parseJsonToArray($data['relationships']['devices']['data'], 'devices');
                        $preset->devices()->sync($dataDevices);
                    } catch (\Exception $e) {
                        $success = false;
                        $error['errors']['devices'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'Preset', 'option' => 'created', 'include' => 'Devices']);
                        //$error['errors']['Message'] = $e->getMessage();
                    }
                }
            }
        }

        if ($success) {
            DB::commit();

            return $this->response()->item($preset, new PresetTransformer(), ['key' => 'presets'])->setStatusCode($this->status_codes['created']);
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
            $error['errors']['preset'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'Preset', 'option' => 'created', 'include' => '']);
            //$error['errors']['Message'] = $e->getMessage();
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }

        if (isset($data['relationships'])) {
            if (isset($data['relationships']['images'])) {
                if (isset($data['relationships']['images']['data'])) {
                    try {
                        $dataImages = $this->parseJsonToArray($data['relationships']['images']['data'], 'images');
                        $preset->images()->sync($dataImages);
                    } catch (\Exception $e) {
                        $success = false;
                        $error['errors']['images'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'Preset', 'option' => 'created', 'include' => 'Images']);
                        //$error['errors']['Message'] = $e->getMessage();
                    }
                }
            }

            if (isset($data['relationships']['devices'])) {
                if (isset($data['relationships']['devices']['data'])) {
                    try {
                        $dataDevices = $this->parseJsonToArray($data['relationships']['devices']['data'], 'devices');
                        $preset->devices()->sync($dataDevices);
                    } catch (\Exception $e) {
                        $success = false;
                        $error['errors']['devices'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'Preset', 'option' => 'created', 'include' => 'Devices']);
                        //$error['errors']['Message'] = $e->getMessage();
                    }
                }
            }
        }

        if ($success) {
            DB::commit();

            return $this->response()->item($preset, new PresetTransformer(), ['key' => 'presets'])->setStatusCode($this->status_codes['created']);
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
