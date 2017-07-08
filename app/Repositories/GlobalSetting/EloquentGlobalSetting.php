<?php

namespace WA\Repositories\GlobalSetting;

use Illuminate\Database\Eloquent\Model;
use WA\Repositories\AbstractRepository;

class EloquentGlobalSetting extends AbstractRepository implements GlobalSettingInterface
{
    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Update a GlobalSetting value.
     *
     * @param int   $id
     * @param array $data
     *
     * @return bool
     */
    public function update(array $data)
    {
        $udl = $this->model->find($data['id']);

        if (!$udl) {
            return 'notExist';
        }

        $udl->name =        isset($data['name'])        ? $data['name']         : $device->name;
        $udl->label =       isset($data['label'])       ? $data['label']        : $device->label;
        $udl->description = isset($data['description']) ? $data['description']  : $device->description;
        $udl->forType =     isset($data['forType'])     ? $data['forType']      : $device->forType;

        if (!$udl->save()) {
            return 'notSaved';
        }

        return $udl;
    }

    /**
     * Create udl values for companies.
     *
     * @param string $name
     * @param int    $companyId
     * @param string $label
     *
     * @return bool
     */
    public function create(array $data)
    {
        return $this->model->firstOrCreate(
                [
                    'name'          => $data['name'],
                    'label'         => $data['label'],
                    'description'   => $data['description'],
                    'forType'       => $data['forType'],
                ]
            );
    }


    /**
     * Retrieve the filters for the Model.
     *
     * @param int  $companyId
     *
     * @return Array
     */
    public function addFilterToTheRequest($companyId) {
        $aux['companyId']= (string) $companyId;
        return $aux;
    }
}
