<?php

namespace WA\Repositories\Modification;

use WA\Repositories\AbstractRepository;

/**
 * Class EloquentModification.
 */
class EloquentModification extends AbstractRepository implements ModificationInterface
{
    /**
     * Update Modification.
     *
     * @param array $data
     *
     * @return bool
     */
    public function update(array $data)
    {
        $modification = $this->model->find($data['id']);

        if (!$modification) {
            return 'notExist';
        }

        if (isset($data['modType'])) {
            $modification->modType = $data['modType'];
        }
        if (isset($data['value'])) {
            $modification->value = $data['value'];
        }

        if (!$modification->save()) {
            return 'notSaved';
        }

        return $modification;
    }

    /**
     * Get an array of all the available modification.
     *
     * @return array of modification
     */
    public function getAllmodification()
    {
        $modification = $this->model->all();

        return $modification;
    }

    /**
     * Create a new modification.
     *
     * @param array $data
     *
     * @return bool|static
     */
    public function create(array $data)
    {
        $modificationData = [
            'modType' => isset($data['modType']) ? $data['modType'] : null,
            'value' => isset($data['value']) ? $data['value'] : null,
        ];

        $modification = $this->model->create($modificationData);

        if (!$modification) {
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

    /**
     * Retrieve the filters for the Model.
     *
     * @param int  $companyId
     *
     * @return Array
     */
    public function addFilterToTheRequest($companyId) {
        //$aux['companies.id'] = (string) $companyId;
        return ''; //$aux;
    }

    /**
     * Check if the Model and/or its relationships are related to the Company of the User.
     *
     * @param JSON  $json : The Json request.
     * @param int  $companyId
     *
     * @return Boolean
     */
    public function checkModelAndRelationships($json, $companyId) {
        return true;
    }

    /**
     * Add the attributes or the relationships needed.
     *
     * @param $data : The Data request.
     *
     * @return $data: The Data with the minimum relationship needed.
     */
    public function addRelationships($data) {
        return $data;
    }
}
