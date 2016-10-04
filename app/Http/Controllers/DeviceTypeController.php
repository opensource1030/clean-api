<?php
namespace WA\Http\Controllers;

use Illuminate\Http\Request;
use WA\DataStore\DeviceType\DeviceType;
use WA\DataStore\DeviceType\DeviceTypeTransformer;
use WA\Repositories\DeviceType\DeviceTypeInterface;

/**
 * DeviceType resource.
 *
 * @Resource("DeviceType", uri="/DeviceType")
 */
class DeviceTypeController extends ApiController
{
    /**
     * @var DeviceTypeInterface
     */
    protected $deviceType;

    /**
     * DeviceType Controller constructor
     *
     * @param DeviceTypeInterface $deviceType
     */
    public function __construct(DeviceTypeInterface $deviceType)
    {
        $this->deviceType = $deviceType;
    }

    /**
     * Show all DeviceType
     *
     * Get a payload of all DeviceType
     *
     */
    public function index()
    {
        $criteria = $this->getRequestCriteria();
        $this->deviceType->setCriteria($criteria);
        $deviceTypes = $this->deviceType->byPage();

        $response = $this->response()->withPaginator($deviceTypes, new DeviceTypeTransformer(),
            ['key' => 'devicetypes']);
        $response = $this->applyMeta($response);
        return $response;
    }

    /**
     * Show a single DeviceType
     *
     * Get a payload of a single DeviceType
     *
     * @Get("/{id}")
     */
    public function show($id)
    {
        $criteria = $this->getRequestCriteria();
        $this->deviceType->setCriteria($criteria);
        $deviceType = $this->deviceType->byId($id);

        if ($deviceType == null) {
            $error['errors']['get'] = 'the DeviceType selected doesn\'t exists';
            return response()->json($error)->setStatusCode(409);
        }

        return $this->response()->item($deviceType, new DeviceTypeTransformer(), ['key' => 'devicetypes']);
    }

    /**
     * Update contents of a DeviceType
     *
     * @param $id
     * @return \Dingo\Api\Http\Response
     */
    public function store($id, Request $request)
    {
        $data = $request->all();
        $data['id'] = $id;
        $deviceType = $this->deviceType->update($data);
        return $this->response()->item($deviceType, new DeviceTypeTransformer(), ['key' => 'devicetypes']);
    }

    /**
     * Create a new DeviceType
     *
     * @return \Dingo\Api\Http\Response
     */
    public function create(Request $request)
    {
        $data = $request->all();
        $deviceType = $this->deviceType->create($data);
        return $this->response()->item($deviceType, new DeviceTypeTransformer(), ['key' => 'devicetypes']);
    }

    /**
     * Delete an DeviceType
     *
     * @param $id
     */
    public function delete($id)
    {
        $deviceType = DeviceType::find($id);
        if ($deviceType <> null) {
            $this->deviceType->deleteById($id);
        } else {
            $error['errors']['delete'] = 'the DeviceType selected doesn\'t exists';
            return response()->json($error)->setStatusCode(409);
        }

        $this->index();
        $deviceType = DeviceType::find($id);
        if ($deviceType == null) {
            return array("success" => true);
        } else {
            $error['errors']['delete'] = 'the DeviceType has not been deleted';
            return response()->json($error)->setStatusCode(409);
        }
    }
}
