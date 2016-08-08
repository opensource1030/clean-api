<?php

namespace WA\Services\Form\DeviceType;

use WA\Repositories\DeviceType\DeviceTypeInterface;
use WA\Services\Form\AbstractForm;

/**
 * Class DeviceTypeForm.
 */
class DeviceTypeForm extends AbstractForm
{
    /**
     * @var DeviceTypeInterface
     */
    protected $deviceType;

    /**
     * @param DeviceTypeInterface $deviceType
     */
    public function __construct(DeviceTypeInterface $deviceType)
    {
        parent::__construct();

        $this->deviceType = $deviceType;
    }

    /**
     * @param $id
     *
     * @return Object
     */
    public function getDeviceById($id)
    {
        return $this->deviceType->byId($id);
    }
}
