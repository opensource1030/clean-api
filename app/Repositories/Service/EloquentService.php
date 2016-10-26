<?php

namespace WA\Repositories\Service;

use WA\Repositories\AbstractRepository;

/**
 * Class EloquentService.
 */
class EloquentService extends AbstractRepository implements ServiceInterface
{
    /**
     * Update Service.
     *
     * @param array $data
     *
     * @return bool
     */
    public function update(array $data)
    {
        $service = $this->model->find($data['id']);

        if (!$service) {
            return 'notExist';
        }

        if (isset($data['title'])) {
            $service->title = $data['title'];
        }
        if (isset($data['planCode'])) {
            $service->planCode = $data['planCode'];
        }
        if (isset($data['cost'])) {
            $service->cost = $data['cost'];
        }
        if (isset($data['description'])) {
            $service->description = $data['description'];
        }
        if (isset($data['domesticMinutes'])) {
            $service->domesticMinutes = $data['domesticMinutes'];
        }
        if (isset($data['domesticData'])) {
            $service->domesticData = $data['domesticData'];
        }
        if (isset($data['domesticMessages'])) {
            $service->domesticMessages = $data['domesticMessages'];
        }
        if (isset($data['internationalMinutes'])) {
            $service->internationalMinutes = $data['internationalMinutes'];
        }
        if (isset($data['internationalData'])) {
            $service->internationalData = $data['internationalData'];
        }
        if (isset($data['internationalMessages'])) {
            $service->internationalMessages = $data['internationalMessages'];
        }

        if (!$service->save()) {
            return 'notSaved';
        }

        return $service;
    }

    /**
     * Get an array of all the available service.
     *
     * @return array of Service
     */
    public function getAllservice()
    {
        $service = $this->model->all();

        return $service;
    }

    /**
     * Create a new Service.
     *
     * @param array $data
     *
     * @return bool|static
     */
    public function create(array $data)
    {
        $serviceData = [
            'title' => isset($data['title']) ? $data['title'] : null,
            'planCode' => isset($data['planCode']) ? $data['planCode'] : 0,
            'cost' => isset($data['cost']) ? $data['cost'] : 0,
            'description' => isset($data['description']) ? $data['description'] : null,
            'domesticMinutes' => isset($data['domesticMinutes']) ? $data['domesticMinutes'] : 0,
            'domesticData' => isset($data['domesticData']) ? $data['domesticData'] : 0,
            'domesticMessages' => isset($data['domesticMessages']) ? $data['domesticMessages'] : 0,
            'internationalMinutes' => isset($data['internationalMinutes']) ? $data['internationalMinutes'] : 0,
            'internationalData' => isset($data['internationalData']) ? $data['internationalData'] : 0,
            'internationalMessages' => isset($data['internationalMessages']) ? $data['internationalMessages'] : 0,
        ];

        $service = $this->model->create($serviceData);

        if (!$service) {
            return false;
        }

        return $service;
    }

    /**
     * Delete a Service.
     *
     * @param int  $id
     * @param bool $soft true soft deletes
     *
     * @return bool
     */
    public function delete($id, $soft = true)
    {
        if (!$this->model->find($id)) {
            return false;
        }

        if (!$soft) {
            $this->model->forceDelete($id);
        }

        return $this->model->destroy($id);
    }
}
