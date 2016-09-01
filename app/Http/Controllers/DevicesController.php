<?php

namespace WA\Http\Controllers;

use Cartalyst\DataGrid\Laravel\Facades\DataGrid;
use WA\DataStore\Device\DeviceTransformer;
use WA\Repositories\Device\DeviceInterface;

/**
 * Devices resource.
 *
 * @Resource("Devices", uri="/devices")
 */
class DevicesController extends ApiController
{
    /**
     * @var DeviceInterface
     */
    protected $device;

    /**
     * Package Controller constructor
     *
     * @param DeviceInterface $device
     */
    public function __construct(DeviceInterface $device)
    {
        $this->device = $device;
    }

    /**
     * Show all devices
     *
     * Get a payload of all devices
     *
     * @Get("/")
     * @Parameters({
     *      @Parameter("page", description="The page of results to view.", default=1),
     *      @Parameter("limit", description="The amount of results per page.", default=10),
     *      @Parameter("access_token", required=true, description="Access token for authentication")
     * })
     */
    public function index()
    {
        $this->getSortAndFilters();

        $this->device->setSort($this->sort)->setFilters($this->filters);

        $devices = $this->device->byPage();

        return $this->response()->withPaginator($devices, new DeviceTransformer(),
            ['key' => 'devices'])->addMeta('sort', $this->sort->get())->addMeta('filter', $this->filters);

    }

    /**
     * Show a single users
     *
     * Get a payload of a single devices by it's ID
     *
     * @Get("/{id}")
     */
    public function show($id)
    {
        $device = $this->device->byId($id);

        return $this->response()->item($device, new DeviceTransformer(), ['key' => 'devices']);
    }


    public function datatable()
    {

        $this->setLimits();

        $devices = $this->model->getDataTable();
        $columns = [
            'devices.id'             => 'id',
            'devices.identification' => 'identification',
            'device_types.make'      => 'make',
            'device_types.model'     => 'model',
            'device_types.class'     => 'class',
        ];

        $options = [
            'throttle' => $this->defaultQueryParams['_perPage'],
            'method'   => $this->defaultQueryParams['_method'],
        ];

        $this->setLimits();

        $response = DataGrid::make($devices, $columns, $options);

        return $response;
    }

    /**
     * Update contents of a device
     *
     * @param $id
     * @return \Dingo\Api\Http\Response
     */
    public function store($id, Request $request)
    {
        $data = $request->all();
        $data['id'] = $id;
        $device = $this->device->update($data);
        return $this->response()->item($device, new DeviceTransformer(), ['key' => 'device']);
    }

    /**
     * Create a new device
     *
     * @return \Dingo\Api\Http\Response
     */
    public function create(Request $request)
    {
        $data = $request->all();
        $device = $this->device->create($data);
        return $this->response()->item($device, new DeviceTransformer(), ['key' => 'device']);
    }

    /**
     * Delete a device
     *
     * @param $id
     */
    public function delete($id)
    {
        $this->device->deleteById($id);
        $this->index();
    }
}
