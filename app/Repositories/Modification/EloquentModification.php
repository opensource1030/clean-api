<?php

namespace WA\Repositories\Modification;

use WA\Repositories\AbstractRepository;

/**
 * Class EloquentModification
 *
 * @package WA\Repositories\Modification
 */
class EloquentModification extends AbstractRepository implements ModificationInterface
{
    /**
     * Update Modification
     *
     * @param array $data
     * @return bool
     */
    public function update(array $data)
    {
        $modification = $this->model->find($data['id']);

        if(!$modification)
        {
            return false;
        }

        if(isset($data['type'])){
            $modification->type = $data['type'];    
        }
        if(isset($data['value'])){
            $modification->value = $data['value'];    
        }

        if(!$modification->save()) {
            return false;
        }

        return $modification;
    }

    /**
     * Get an array of all the available modification.
     *
     * @return Array of modification
     */
    public function getAllmodification()
    {
        $modification =  $this->model->all();
        return $modification;
    }

    /**
     * Create a new modification
     *
     * @param array $data
     * @return bool|static
     */
    public function create(array $data)
    {
        $modificationData = [
            "type" =>  isset($data['type']) ? $data['type'] : null ,
            "value" => isset($data['value']) ? $data['value'] : null,
        ];

        $modification = $this->model->create($modificationData);

        if(!$modification) {
            return false;
        }

        return $modification;
    }

    /**
     * Delete a modification.
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