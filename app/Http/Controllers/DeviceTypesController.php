<?php

namespace WA\Http\Controllers;

use WA\DataStore\DeviceType\DeviceType;
use WA\DataStore\DeviceType\DeviceTypeTransformer;
use WA\Repositories\DeviceType\DeviceTypeInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

/**
 * DeviceType resource.
 *
 * @Resource("DeviceType", uri="/DeviceType")
 */
class DeviceTypesController extends ApiController
{
    /**
     * @var DeviceTypeInterface
     */
    protected $deviceType;

    /**
     * DeviceType Controller constructor.
     *
     * @param DeviceTypeInterface $deviceType
     */
    public function __construct(DeviceTypeInterface $deviceType)
    {
        $this->deviceType = $deviceType;
    }

    /**
     * Show all DeviceType.
     *
     * Get a payload of all DeviceType
     */
    public function index()
    {
        $criteria = $this->getRequestCriteria();
        $this->deviceType->setCriteria($criteria);
        $deviceTypes = $this->deviceType->byPage();

        $response = $this->response()->withPaginator($deviceTypes, new DeviceTypeTransformer(), ['key' => 'devicetypes']);
        $response = $this->applyMeta($response);

        return $response;
    }

    /**
     * Show a single DeviceType.
     *
     * Get a payload of a single DeviceType
     *
     * @Get("/{id}")
     */
    public function show($id)
    {
        $criteria = $this->getRequestCriteria();
        $this->deviceType->setCriteria($criteria);
        $deviceType = DeviceType::find($id);

        if ($deviceType == null) {
            $error['errors']['get'] = Lang::get('messages.NotExistClass', ['class' => 'DeviceType']);
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }

        return $this->response()->item($deviceType, new DeviceTypeTransformer(), ['key' => 'devicetypes'])->setStatusCode($this->status_codes['created']);
    }

    /**
     * Update contents of a DeviceType.
     *
     * @param $id
     *
     * @return \Dingo\Api\Http\Response
     */
    public function store($id, Request $request)
    {
        if ($this->isJsonCorrect($request, 'devicetypes')) {
            try {
                $data = $request->all()['data']['attributes'];
                $data['id'] = $id;
                $devicetype = $this->deviceType->update($data);

                if ($devicetype == 'notExist') {
                    $error['errors']['devicetype'] = Lang::get('messages.NotExistClass', ['class' => 'DeviceType']);
                    //$error['errors']['Message'] = $e->getMessage();
                    return response()->json($error)->setStatusCode($this->status_codes['notexists']);
                }

                if ($devicetype == 'notSaved') {
                    $error['errors']['devicetype'] = Lang::get('messages.NotSavedClass', ['class' => 'DeviceType']);
                    //$error['errors']['Message'] = $e->getMessage();
                    return response()->json($error)->setStatusCode($this->status_codes['conflict']);
                }

                return $this->response()->item($devicetype, new DeviceTypeTransformer(), ['key' => 'devicetypes'])->setStatusCode($this->status_codes['created']);
            } catch (\Exception $e) {
                $error['errors']['devicetypes'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'DeviceType', 'option' => 'updated', 'include' => '']);
                //$error['errors']['Message'] = $e->getMessage();
            }
        } else {
            $error['errors']['json'] = Lang::get('messages.InvalidJson');
        }

        return response()->json($error)->setStatusCode($this->status_codes['conflict']);
    }

    /**
     * Create a new DeviceType.
     *
     * @return \Dingo\Api\Http\Response
     */
    public function create(Request $request)
    {
        if ($this->isJsonCorrect($request, 'devicetypes')) {
            try {
                $data = $request->all()['data']['attributes'];
                $devicetype = $this->deviceType->create($data);

                return $this->response()->item($devicetype, new DeviceTypeTransformer(), ['key' => 'devicetypes'])->setStatusCode($this->status_codes['created']);
            } catch (\Exception $e) {
                $error['errors']['devicetypes'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'DeviceType', 'option' => 'created', 'include' => '']);
                //$error['errors']['Message'] = $e->getMessage();
            }
        } else {
            $error['errors']['json'] = Lang::get('messages.InvalidJson');
        }

        return response()->json($error)->setStatusCode($this->status_codes['conflict']);
    }

    /**
     * Delete an DeviceType.
     *
     * @param $id
     */
    public function delete($id)
    {
        $deviceType = DeviceType::find($id);
        if ($deviceType != null) {
            $this->deviceType->deleteById($id);
        } else {
            $error['errors']['delete'] = Lang::get('messages.NotExistClass', ['class' => 'DeviceType']);
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }

        $deviceType = DeviceType::find($id);
        if ($deviceType == null) {
            return array("success" => true);
        } else {
            $error['errors']['delete'] = Lang::get('messages.NotDeletedClass', ['class' => 'DeviceType']);
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }
    }
}
