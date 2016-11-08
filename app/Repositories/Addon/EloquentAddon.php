<?php

namespace WA\Repositories\Addon;

use WA\Repositories\AbstractRepository;

/**
 * Class Eloquentaddon.
 */
class EloquentAddon extends AbstractRepository implements AddonInterface
{
    /**
     * Update addon.
     *
     * @param array $data
     *
     * @return bool
     */
    public function update(array $data)
    {
        $addon = $this->model->find($data['id']);

        if (!$addon) {
            return 'notExist';
        }

        if (isset($data['name'])) {
            $addon->name = $data['name'];
        }
        if (isset($data['cost'])) {
            $addon->cost = $data['cost'];
        }
        if (isset($data['serviceId'])) {
            $addon->serviceId = $data['serviceId'];
        }

        if (!$addon->save()) {
            return 'notSaved';
        }

        return $addon;
    }

    /**
     * Get an array of all the available addon.
     *
     * @return array of addon
     */
    public function getAllAddon()
    {
        $addon = $this->model->all();

        return $addon;
    }

    /**
     * Create a new addon.
     *
     * @param array $data
     *
     * @return bool|static
     */
    public function create(array $data)
    {
        $addonData = [
            "name" =>  isset($data['name']) ? $data['name'] : null ,
            "cost" => isset($data['cost']) ? $data['cost'] : null,
            "serviceId" => isset($data['serviceId']) ? $data['serviceId'] : null,
        ];

        $addon = $this->model->create($addonData);

        if (!$addon) {
            return false;
        }

        return $addon;
    }

    /**
     * Delete a addon.
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
