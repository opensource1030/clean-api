<?php

namespace WA\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use WA\DataStore\Category\Preset;
use WA\DataStore\Category\PresetTransformer;
use WA\Repositories\Category\PresetInterface;

/**
 * Preset resource.
 *
 * @Resource("preset", uri="/preset")
 */
class PresetController extends ApiController
{
    /**
     * @var PresetInterface
     */
    protected $preset;

    /**
     * Preset Controller constructor
     *
     * @param PresetInterface $preset
     */
    public function __construct(PresetInterface $preset)
    {

        $this->preset = $preset;
    }

    /**
     * Show all Preset
     *
     * Get a payload of all Preset
     *
     */
    public function index()
    {

        $criteria = $this->getRequestCriteria();
        $this->preset->setCriteria($criteria);
        $preset = $this->preset->byPage();

        $response = $this->response()->withPaginator($preset, new PresetTransformer(), ['key' => 'preset']);
        $response = $this->applyMeta($response);
        return $response;
    }

    /**
     * Show a single Preset
     *
     * Get a payload of a single Preset
     *
     * @Get("/{id}")
     */
    public function show($id, Request $request)
    {

        $criteria = $this->getRequestCriteria();
        $this->preset->setCriteria($criteria);
        $preset = $this->preset->byId($id);

        if ($preset == null) {
            $error['errors']['get'] = 'the Preset selected doesn\'t exists';
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }

        if (!$this->includesAreCorrect($request, new PresetTransformer())) {
            $error['errors']['getincludes'] = 'One or More Includes selected doesn\'t exists';
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }

        return $this->response()->item($preset, new PresetTransformer(),
            ['key' => 'presets'])->setStatusCode($this->status_codes['created']);
    }

    /**
     * Update contents of a Preset
     *
     * @param $id
     * @return \Dingo\Api\Http\Response
     */
    public function store($id, Request $request)
    {

        /*
         * Checks if Json has data, data-type & data-attributes.
         */
        if (!$this->isJsonCorrect($request, 'presets')) {
            $error['errors']['json'] = 'Json is Invalid';
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }

        DB::beginTransaction();

        try {
            $data = $request->all()['data']['attributes'];
            $data['id'] = $id;
            $preset = $this->preset->update($data);
        } catch (\Exception $e) {
            DB::rollBack();
            $error['errors']['preset'] = 'The Preset has not been updated';
            //$error['errors']['presetMessage'] = $e->getMessage();
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }

        if (isset($data['relationships'])) {
            if (isset($data['relationships']['images'])) {
                if (isset($data['relationships']['images']['data'])) {
                    try {
                        $dataImages = $this->parseJsonToArray($data['relationships']['images']['data'], 'images');
                        $preset->images()->sync($dataImages);
                    } catch (\Exception $e) {
                        DB::rollBack();
                        $error['errors']['images'] = 'the Preset Images has not been created';
                        //$error['errors']['imagesMessage'] = $e->getMessage();
                        return response()->json($error)->setStatusCode($this->status_codes['conflict']);
                    }
                }
            }

            if (isset($data['relationships']['devices'])) {
                if (isset($data['relationships']['devices']['data'])) {
                    try {
                        $dataDevices = $this->parseJsonToArray($data['relationships']['devices']['data'], 'devices');
                        $preset->devices()->sync($dataDevices);
                    } catch (\Exception $e) {
                        DB::rollBack();
                        $error['errors']['devices'] = 'the Preset Devices has not been created';
                        //$error['errors']['devicesMessage'] = $e->getMessage();
                        return response()->json($error)->setStatusCode($this->status_codes['conflict']);
                    }
                }
            }
        }

        DB::commit();
        return $this->response()->item($preset, new PresetTransformer(),
            ['key' => 'presets'])->setStatusCode($this->status_codes['created']);
    }

    /**
     * Create a new Preset
     *
     * @return \Dingo\Api\Http\Response
     */
    public function create(Request $request)
    {

        /*
         * Checks if Json has data, data-type & data-attributes.
         */
        if (!$this->isJsonCorrect($request, 'presets')) {
            $error['errors']['json'] = 'Json is Invalid';
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }

        DB::beginTransaction();

        try {
            $data = $request->all()['data']['attributes'];
            $preset = $this->preset->create($data);
        } catch (\Exception $e) {
            DB::rollBack();
            $error['errors']['preset'] = 'The Preset has not been created';
            //$error['errors']['presetMessage'] = $e->getMessage();
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }

        if (isset($data['relationships'])) {
            if (isset($data['relationships']['images'])) {
                if (isset($data['relationships']['images']['data'])) {
                    try {
                        $dataImages = $this->parseJsonToArray($data['relationships']['images']['data'], 'images');
                        $preset->images()->sync($dataImages);
                    } catch (\Exception $e) {
                        DB::rollBack();
                        $error['errors']['images'] = 'the Preset Images has not been created';
                        //$error['errors']['imagesMessage'] = $e->getMessage();
                        return response()->json($error)->setStatusCode($this->status_codes['conflict']);
                    }
                }
            }

            if (isset($data['relationships']['devices'])) {
                if (isset($data['relationships']['devices']['data'])) {
                    try {
                        $dataDevices = $this->parseJsonToArray($data['relationships']['devices']['data'], 'devices');
                        $preset->devices()->sync($dataDevices);
                    } catch (\Exception $e) {
                        DB::rollBack();
                        $error['errors']['devices'] = 'the Preset Devices has not been created';
                        //$error['errors']['devicesMessage'] = $e->getMessage();
                        return response()->json($error)->setStatusCode($this->status_codes['conflict']);
                    }
                }
            }
        }

        DB::commit();
        return $this->response()->item($preset, new PresetTransformer(),
            ['key' => 'presets'])->setStatusCode($this->status_codes['created']);
    }

    /**
     * Delete a Preset
     *
     * @param $id
     */
    public function delete($id)
    {

        $preset = Preset::find($id);
        if ($preset <> null) {
            $this->preset->deleteById($id);
        } else {
            $error['errors']['delete'] = 'the Preset selected doesn\'t exists';
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }

        $this->index();
        $preset = Preset::find($id);
        if ($preset == null) {
            return array("success" => true);
        } else {
            $error['errors']['delete'] = 'the Preset has not been deleted';
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }
    }
}
