<?php

namespace WA\Repositories\Category;

use WA\Repositories\AbstractRepository;

class EloquentPreset extends AbstractRepository implements PresetInterface
{
    /**
     * Update preset.
     *
     * @param array $data
     *
     * @return bool
     */
    public function update(array $data)
    {
        if(!isset($data['id'])) {
            return 'notExist';
        }

        $preset = $this->model->find($data['id']);

        if (!$preset) {
            return 'notExist';
        }

        if (isset($data['name'])) {
            $preset->name = $data['name'];
        }

        if (!$preset->save()) {
            return 'notSaved';
        }

        return $preset;
    }

    /**
     * Get an array of all the available preset.
     *
     * @return array of preset
     */
    public function getAllPresets()
    {
        $preset = $this->model->all();

        return $preset;
    }

    /**
     * Create a new preset.
     *
     * @param array $data
     *
     * @return bool|static
     */
    public function create(array $data)
    {
        if (!isset($data['name']) || $data['name'] == '') {
            return false; // return an error if the name doesn't exist or is empty.
        }
    
        $user = \Auth::user();
        if ($user === null) {
            if (isset($data['companyId'])) {
                $companyId = $data['companyId']; 
            } else {
                dd("?");
                return false;
            }            
        } else {
            $companyId = $user->companyId; // Retrieve the companyId from the user.        
        }

        $presetData = [
            'name' => $data['name'],
            'companyId' => $companyId
        ];



        $preset = $this->model->create($presetData);

        return $preset;
    }

    /**
     * Delete a preset.
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
