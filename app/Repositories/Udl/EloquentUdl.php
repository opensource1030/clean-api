<?php

namespace WA\Repositories\Udl;

use Illuminate\Database\Eloquent\Model;
use WA\Repositories\UdlValue\UdlValueInterface;

class EloquentUdl implements UdlInterface
{
    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * @var \WA\Repositories\UdlValue\UdlValueInterface
     */
    protected $udlValue;

    /**
     * @param Model $model
     */
    public function __construct(Model $model, UdlValueInterface $udlValue)
    {
        $this->model = $model;
        $this->udlValue = $udlValue;
    }

    /**
     * UDL by the id.
     *
     * @param int $id
     *
     * @return Object object of the UDL information
     */
    public function byId($id)
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Get the UDL information by the name.
     *
     * @param string $name
     *
     * @return Object of the UDL information
     */
    public function byLabel($name)
    {
        return $this->model->where('name', $name)->first();
    }

    /**
     * Get the UDL values of a UDL.
     *
     * @param int $id
     *
     * @return Object object information of the UDL Values
     */
    public function byUDLValue($id)
    {
        // TODO: Implement byUDLValue() method.
    }

    /**
     * Update a UDL value.
     *
     * @param int   $id
     * @param array $data
     *
     * @return bool
     */
    public function update($id, array $data)
    {
        // TODO: Implement update() method.
    }

    /**
     * Delete a UDL (soft deletes).
     *
     * @param int $id of the UDL
     *
     * @return bool
     */
    public function delete($id)
    {
        // TODO: Implement delete() method.
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
    public function create($name, $companyId, $label)
    {
        return
            $this->model->firstOrCreate(
                [
                    'name' => $name,
                    'companyId' => $companyId,
                    'label' => $label,
                ]
            );
    }

    /**
     * Get the UDL information by the name.
     *
     * @param string $name
     * @param int $companyId
     *
     * @return Object of the UDL information
     */
    public function byName($name, $companyId = null)
    {
        $udl = $this->model->where('name', $name);

        if(isset($companyId))
        {
            $udl->where('companyId', $companyId);
        }

        $name = $udl->first();

        return  $name;
    }

    /**
     * Get UDL by Company ID.
     *
     * @param int $companyId
     * @param bool udlValues
     * @param bool $api
     *
     * @return array of UDL and Values, it's keyed  as: [udlName__id] = udlValueName
     */
    public function byCompanyId($companyId, $udlValues = true, $api = false)
    {
        if ($api) {
            $model = $this->model->where('companyId', $companyId);

            return $model->get();
        }

        $response = [];

        $model = $this->model;
        $udls = $model->where('companyId', $companyId)->orderBy('sortOrder', 'ASC')->get([
            'id',
            'name',
            'label',
            'requiredForHelpDesk',
        ]);

        if (is_null($udls)) {
            return [];
        }

        if (!$udlValues) {
            return $response = $udls->toArray();
        }

        foreach ($udls->toArray() as $udl) {
            $response[$udl['label'].'__'.$udl['id'].'__'.$udl['requiredForHelpDesk']] = $this->getValueOfUDL($udl['id']);
        }

        return $response;
    }

    /**
     * @param $udlId
     *
     * @return Object of UDL Values
     */
    protected function getValueOfUDL($udlId)
    {
        $udlValue = $this->udlValue->byUdlId($udlId)->toArray();

        return $udlValue;
    }
}
