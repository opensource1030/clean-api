<?php

namespace WA\Repositories\Address;

use WA\Repositories\AbstractRepository;
use WA\DataStore\Address\Address;

/**
 * Class EloquentAddress.
 */
class EloquentAddress extends AbstractRepository implements AddressInterface
{
    /**
     * Update Address.
     *
     * @param array $data
     *
     * @return bool
     */
    public function update(array $data)
    {
        $address = $this->model->find($data['id']);

        if (!$address) {
            return 'notExist';
        }

        if (isset($data['name'])) {
            $address->name = $data['name'];
        }
        if (isset($data['attn'])) {
            $address->attn = $data['attn'];
        }
        if (isset($data['phone'])) {
            $address->phone = $data['phone'];
        }
        if (isset($data['address'])) {
            $address->address = $data['address'];
        }
        if (isset($data['city'])) {
            $address->city = $data['city'];
        }
        if (isset($data['state'])) {
            $address->state = $data['state'];
        }
        if (isset($data['country'])) {
            $address->country = $data['country'];
        }
        if (isset($data['postalCode'])) {
            $address->postalCode = $data['postalCode'];
        }

        if (!$address->save()) {
            return 'notSaved';
        }

        return $address;
    }

    /**
     * Get an array of all the available Address.
     *
     * @return array of Address
     */
    public function getAllAddress()
    {
        $address = $this->model->all();

        return $address;
    }

    /**
     * Create a new Address.
     *
     * @param array $data
     *
     * @return bool|static
     */
    public function create(array $data)
    {
        $addressData = [
            "name" => isset($data['name']) ? $data['name'] : '',
            "attn" => isset($data['attn']) ? $data['attn'] : '',
            "phone" => isset($data['phone']) ? $data['phone'] : '',
            "address" =>  isset($data['address']) ? $data['address'] : '' ,
            "city" => isset($data['city']) ? $data['city'] : '',
            "state" => isset($data['state']) ? $data['state'] : '',
            "country" => isset($data['country']) ? $data['country'] : '',
            "postalCode" => isset($data['postalCode']) ? $data['postalCode'] : '',
        ];

        $address = $this->model->create($addressData);

        if (!$address) {
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

    /**
     * Retrieve the filters for the Model.
     *
     * @param int  $companyId
     *
     * @return Array
     */
    public function addFilterToTheRequest($companyId) {
        $aux[] = '[companies.id]=' . (string) $companyId . '[or][users.companyId]=' . (string) $companyId;
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
        if(!isset($json->data->relationships)) {
            return false;
        } else {
            $relations = $json->data->relationships;
            if (!isset($relations->companies) && !isset($relations->users)) {
                return false;
            }

            if (isset($relations->companies)) {
                $companyOk = false;
                foreach ($relations->companies->data as $value) {
                    if ($value->type == 'companies' && $value->id == $companyId) {
                        $companyOk = true;
                    }
                }
                if (!$companyOk) {
                    return false;
                }
            }
            
            if (isset($relations->users)) {
                $userOk = false;
                foreach ($relations->users->data as $value) {
                    if ($value->type == 'users'){
                        $user = \WA\DataStore\User\User::find($value->id);
                        if ($user->companyId == $companyId) {
                            $userOk = true;
                        }
                    }
                }
                if (!$userOk) {
                    return false;
                }
            }

            return true;
        }
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
