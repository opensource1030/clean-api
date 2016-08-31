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

        if(isset($data['name'])){
            $package->name = $data['name'];
        }
        if(isset($data['conditionsId'])){
            $package->conditionsId = $data['conditionsId'];
        }
        if(isset($data['devicesId'])){
            $package->devicesId = $data['devicesId'];
        }
        if(isset($data['appsId'])){
            $package->appsId = $data['appsId'];
        }
        if(isset($data['servicesId'])){
            $package->servicesId = $data['servicesId'];
        }
        
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
            "conditionsId" => isset($data['conditionsId']) ? $data['conditionsId'] : 0,
            "devicesId" =>  isset($data['devicesId']) ? $data['devicesId'] : 0 ,
            "appsId" => isset($data['appsId']) ? $data['appsId'] : 0,
            "servicesId" => isset($data['servicesId']) ? $data['servicesId'] : 0,
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