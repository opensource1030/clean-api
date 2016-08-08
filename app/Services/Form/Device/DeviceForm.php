<?php

namespace WA\Services\Form\Device;

use Log;
use WA\Repositories\Device\DeviceInterface;
use WA\Services\Form\AbstractForm;

/**
 * Class DeviceForm.
 */
class DeviceForm extends AbstractForm
{
    /**
     * @var DeviceInterface
     */
    protected $device;

    /**
     * @var DeviceFormValidator
     */
    protected $validator;

    /**
     * @param DeviceInterface $device
     * @param DeviceFormValidator $validator
     */
    public function __construct(DeviceInterface $device, DeviceFormValidator $validator)
    {
        $this->device = $device;
        $this->validator = $validator;
    }

    /**
     * @param array $data
     *
     * @return bool
     */
    public function update(array $data)
    {
        if (!$this->valid($data)) {
            $this->errors = $this->validator->errors();
            $this->notify('error', 'This data you entered is invalid');

            return false;
        }

        if (count($data['assets']['new']) > 0) {
            if (!$this->device->syncAsset($data['id'], $data['assets'])) {
                $this->notify('error', 'Assigning device to this asset failed, please try again later');

                return false;
            }
        }

//        $this->notify('success', 'Devices is now assigned to select Asset');

        return true;
    }

    /**
     * Get the device By it's ID.
     *
     * @param $id
     *
     * @return Object of device \ bool
     */
    public function getDeviceById($id)
    {
        try {
            return $this->device->byId($id);
        } catch (\Exception $e) {
            $this->notify('error', 'There was a problem getting a device with that id # ' . $id);
            Log::error('There was an issue getting id ' . $id . ' ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Get the next available pending review.
     *
     * @return int id of the unassigned
     */
    public function getNextUnassigned()
    {
        return $this->device->byUnassigned(false);
    }

    /**
     * @param $id
     *
     * @return Object
     */
    public function edit($id)
    {
        return $this->device->byId($id);
    }

    /**
     * @param $id
     *
     * @return Object
     */
    public function show($id)
    {
        return $this->device->byId($id);
    }
}
