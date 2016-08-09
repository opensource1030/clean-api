<?php

namespace WA\Http\Controllers\Device;

use Auth;
use Illuminate\Http\Request;
use Input;
use Redirect;
use View;
use WA\Http\Controllers\Auth\AuthorizedController;
use WA\Repositories\Device\DeviceInterface;
use WA\Repositories\Location\LocationInterface;


/**
 * Class DeviceController.
 */
class DeviceController extends AuthorizedController
{

    /**
     * @var LocationInterface
     */
    protected $location;

    /**
     * @var DeviceInterface
     */
    protected $device;


    /**
     * DeviceController constructor.
     *
     * @param DeviceInterface $device
     * @param LocationInterface $location
     */
    public function __construct(
        DeviceInterface $device,
        LocationInterface $location

    ) {
        parent::__construct();
        $this->device = $device;
        $this->location = $location;
    }

    /**
     * Create view
     *
     * @return mixed
     */
    public function create()
    {
        return View::make('devices.new');
    }

    /**
     * Show device view
     *
     * @param $id
     *
     * @return mixed
     */
    public function show($id)
    {

        $device = $this->device->byId($id);
        $device->load('deviceType', 'assets', 'users', 'companies');
        $carrierDevice = $device->deviceType->carrierDevice;

        return View::make('devices.show')
            ->with([
                'device'        => $device,
                'carrierDevice' => $carrierDevice
            ]);
    }

    /**
     *
     * Edit device view
     *
     * @param $id
     *
     * @return mixed
     */
    public function edit($id)
    {
        $device = $this->device->byId($id);
        $device->load('deviceType', 'assets', 'users', 'companies');
        $carrierDevice = $device->deviceType->carrierDevice;


        $maxViewRows = 4;
        $perRowCount = 0;


        $this->data = array_merge(
            ['device' => $device],
            ['carrierDevice' => $carrierDevice],
            ['maxViewRows' => $maxViewRows]
        );

        return View::make('devices.edit')->with($this->data);
    }

    /**
     * @param Request $request
     * @param $id
     */
    public function update(Request $request, $id)
    {

    }

    /**
     * @param Request $request
     */
    public function store(Request $request)
    {

    }

    /**
     * Delete device
     *
     * @param $id
     * @return mixed
     */
    public function destroy($id)
    {
        $device = $this->device->byId($id);
        if (!$device->delete()) {
            return Redirect::back();
        }

        return $this->index();
    }

    /**
     * @param $id
     */
    public function sync($id)
    {
        $device = $this->device->byId($id);
        return $device->sync();
    }


    public function index()
    {
        return View::make('devices.index');
    }

}
