<?php

namespace WA\Repositories\Package;

use WA\Repositories\AbstractRepository;

/**
 * Class EloquentPackage
 *
 * @package WA\Repositories\Package
 */
class EloquentPackage extends AbstractRepository implements PackageInterface
{
    /**
     * Update Package.
     *
     * @param array $data
     *
     * @return bool
     */
    public function update(array $data)
    {
        $package = $this->model->find($data['id']);

        if (!$package) {
            return 'notExist';
        }

        if (isset($data['name'])) {
            $package->name = $data['name'];
        }

        if (isset($data['information'])) {
            $package->information = $data['information'];
        }

        if (isset($data['approvalCode'])) {
            $package->approvalCode = $data['approvalCode'];
        }

        if (!$package->save()) {
            return 'notSaved';
        }

        return $package;
    }

    /**
     * Get an array of all the available package.
     *
     * @return Array of Package
     */
    public function getAllPackage()
    {
        $package = $this->model->all();

        return $package;
    }

    /**
     * Get an array of all the available package.
     *
     * @return Array of Package
     */
    public function getAllPackageByCompanyId($companyId)
    {
        $package = $this->model->where('companyId', $companyId)->get();

        return $package;
    }

    /**
     * Create a new Package.
     *
     * @param array $data
     *
     * @return bool|static
     */
    public function create(array $data)
    {
        $packageData = [
            'name' => isset($data['name']) ? $data['name'] : '',
            'information' => isset($data['information']) ? $data['information'] : '',
            'approvalCode' => isset($data['approvalCode']) ? $data['approvalCode'] : null,
            'companyId' => isset($data['companyId']) ? $data['companyId'] : 0,
        ];

        $package = $this->model->create($packageData);

        if (!$package) {
            return false;
        }

        return $package;
    }

    /**
     * Delete a Package.
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
        $aux['companyId'] = (string) $companyId;
        return $aux;
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
        return $json->data->attributes->companyId == $companyId;
    }
}
