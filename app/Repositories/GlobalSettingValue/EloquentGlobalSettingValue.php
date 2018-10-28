<?php

namespace WA\Repositories\UdlValue;

use Illuminate\Database\Eloquent\Model;
use WA\Repositories\AbstractRepository;
use WA\Repositories\GlobalSetting\GlobalSettingInterface;

class EloquentGlobalSettingValue extends AbstractRepository implements GlobalSettingInterface
{
    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Creates a new UDL value.
     *
     * @param array $data
     *
     * @return bool
     */
    public function create(array $data)
    {
//        return
//            $this->model->firstOrCreate(
//                [
//                    'name' => $data['name'],
//                    'udlId' => $data['udlId'],
//                    'externalId' => isset($data['externalId']) ? $data['externalId'] : null,
//                ]
//            );
    }

    public function update(array $data)
    {
        $udlValue = $this->model->find($data['id']);

        if (!$udlValue) {
            return 'notExist';
        }

        $udlValue->name = isset($data['name']) ? $data['name'] : $udlValue->name;
        $udlValue->udlId = isset($data['udlId']) ? $data['udlId'] : $udlValue->udlId;
        $udlValue->externalId = isset($data['externalId']) ? $data['externalId'] : $udlValue->externalId;

        if (!$udlValue->save()) {
            return 'notSaved';
        }

        return $udlValue;
    }
}
