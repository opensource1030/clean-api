<?php

namespace WA\Repositories\Price;

use WA\Repositories\AbstractRepository;

/**
 * Class EloquentPrice
 *
 * @package WA\Repositories\Price
 */
class EloquentPrice extends AbstractRepository implements PriceInterface
{
    /**
     * Update Price
     *
     * @param array $data
     * @return bool
     */
    public function update(array $data)
    {
        $price = $this->model->find($data['id']);

        if(!$price)
        {
            return false;
        }

        if(isset($data['type'])){
            $price->type =  $data['type'];
        }
        if(isset($data['deviceId'])){
            $price->deviceId =  $data['deviceId'];
        }
        if(isset($data['capacityId'])){
            $price->capacityId =  $data['capacityId'];
        }
        if(isset($data['styleId'])){
            $price->styleId =  $data['styleId'];
        }
        if(isset($data['carrierId'])){
            $price->carrierId =  $data['carrierId'];
        }
        if(isset($data['companyId'])){
            $price->companyId =  $data['companyId'];
        }
        if(isset($data['priceRetail'])){
            $price->priceRetail =  $data['priceRetail'];
        }
        if(isset($data['price1'])){
            $price->price1 =  $data['price1'];
        }
        if(isset($data['price2'])){
            $price->price2 =  $data['price2'];        
        }
        if(isset($data['priceOwn'])){
            $price->priceOwn =  $data['priceOwn'];
        }

        if(!$price->save()) {
            return false;
        }

        return $price;
    }

    /**
     * Get an array of all the available Price.
     *
     * @return Array of Price
     */
    public function getAllPrice()
    {
        $prices =  $this->model->all();
        return $prices;
    }

    /**
     * Create a new Price
     *
     * @param array $data
     * @return bool|static
     */
    public function create(array $data)
    {
        $priceData = [
            "type" =>  isset($data['type']) ? $data['type'] : null,
            "deviceId" => isset($data['deviceId']) ? $data['deviceId'] : 0,
            "capacityId" => isset($data['capacityId']) ? $data['capacityId'] : 0,
            "styleId" => isset($data['styleId']) ? $data['styleId'] : 0,
            "carrierId" => isset($data['carrierId']) ? $data['carrierId'] : 0,
            "companyId" => isset($data['companyId']) ? $data['companyId'] : 0,
            "priceRetail" => isset($data['priceRetail']) ? $data['priceRetail'] : 0,
            "price1" => isset($data['price1']) ? $data['price1'] : 0,
            "price2" => isset($data['price2']) ? $data['price2'] : 0,
            "priceOwn" => isset($data['priceOwn']) ? $data['priceOwn'] : 0
        ];

        $price = $this->model->create($priceData);

        if(!$price) {
            return false;
        }

        return $price;
    }

    /**
     * Delete a Price.
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
     * Get an array of all the available Price.
     *
     * @return Array of Price
     */
    public function getPriceDevices($id)
    {
        $prices =  $this->model->where('deviceId', $id)->get();
        /*->take( $filter['numItems'] )->offset( $filter['numItems'] * ( $filter['page'] - 1 ) );*/
        return $prices;
    }

    /**
     * Get an array of all the available Price.
     *
     * @return Array of Price
     */
    public function getPriceCapacities($id)
    {
        $prices =  $this->model->all();
        return $prices;
    }

    /**
     * Get an array of all the available Price.
     *
     * @return Array of Price
     */
    public function getPriceStyles($id)
    {
        $prices =  $this->model->all();
        return $prices;
    }

    /**
     * Get an array of all the available Price.
     *
     * @return Array of Price
     */
    public function getPriceCarriers($id)
    {
        $prices =  $this->model->all();
        return $prices;
    }

    /**
     * Get an array of all the available Price.
     *
     * @return Array of Price
     */
    public function getPriceCompanies($id)
    {
        $prices =  $this->model->all();
        return $prices;
    }

}