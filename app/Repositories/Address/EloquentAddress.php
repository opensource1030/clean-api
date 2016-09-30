<?php

namespace WA\Repositories\Address;

use WA\Repositories\AbstractRepository;

/**
 * Class EloquentAddress
 *
 * @package WA\Repositories\Address
 */
class EloquentAddress extends AbstractRepository implements AddressInterface
{
    /**
     * Update Address
     *
     * @param array $data
     * @return bool
     */
    public function update(array $data)
    {
        $address = $this->model->find($data['id']);

        if(!$address)
        {
            return false;
        }

        if(isset($data['address'])){
            $address->address =  $data['address'];            
        }
        if(isset($data['city'])){
            $address->city =  $data['city'];            
        }
        if(isset($data['state'])){
            $address->state =  $data['state'];            
        }
        if(isset($data['country'])){
            $address->country =  $data['country'];            
        }
        if(isset($data['postalCode'])){
            $address->postalCode =  $data['postalCode'];            
        }

        if(!$address->save()) {
            return false;
        }

        return $address;

    }

    /**
     * Get an array of all the available Address.
     *
     * @return Array of Address
     */
    public function getAllAddress()
    {
        $address =  $this->model->all();
        return $address;
    }

    /**
     * Create a new Address
     *
     * @param array $data
     * @return bool|static
     */
    public function create(array $data)
    {
        $addressData = [
            "address" =>  isset($data['address']) ? $data['address'] : '' ,
            "city" => isset($data['city']) ? $data['city'] : '',
            "state" => isset($data['state']) ? $data['state'] : '',
            "country" => isset($data['country']) ? $data['country'] : '',
            "postalCode" => isset($data['postalCode']) ? $data['postalCode'] : '',
        ];

        $address = $this->model->create($addressData);

        if(!$address) {
            return false;
        }

        return $address;
    }

    /**
     * Delete a Address.
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