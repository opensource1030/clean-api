<?php

namespace WA\Repositories\DeviceType;

use WA\Repositories\AbstractRepository;

class EloquentDeviceType extends AbstractRepository implements DeviceTypeInterface
{
    /**
     * Get the Device Type by is model name.
     *
     * @param $model
     *
     * @return Object object of device type
     */
    public function byModel($model)
    {
        return $this->model->where('model', $model)->first();
    }

    /**
     * Get the Device Type by is model name or create if it doesn't exist.
     *
     * @param string $model
     * @param array  $data
     *
     * @return Object object of device type
     */
    public function byModelOrCreate($model, array $data)
    {
        $type = $this->byModel($model);

        if (!$type) {
            $type = $this->create($data);
        }

        return $type;
    }
}
