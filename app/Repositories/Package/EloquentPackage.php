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
     * Update Package
     *
     * @param array $data
     * @return bool
     */
    public function update(array $data)
    {
        $package = $this->model->find($data['id']);

        if(!$package)
        {
            return false;
        }

        $package->name =  isset($data['name']) ? $data['name'] : null ;
        
        if(!$package->save()) {
            return false;
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
        $package =  $this->model->all();
        return $package;
    }

    /**
     * Create a new Package
     *
     * @param array $data
     * @return bool|static
     */
    public function create(array $data)
    {

        $packageData = [
            "name" =>  isset($data['name']) ? $data['name'] : null ,
        ];

        $package = $this->model->create($packageData);

        if(!$package) {
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
}