<?php

namespace WA\Repositories\Company;

use WA\Repositories\AbstractRepository;

/**
 * Class EloquentCompanySetting.
 */
class EloquentCompanySetting extends AbstractRepository implements CompanySettingInterface
{
    /**
     * Update CompanySetting.
     *
     * @param array $data
     *
     * @return bool
     */
    public function update(array $data)
    {
        $companySetting = $this->model->find($data['id']);

        if (!$companySetting) {
            return 'notExist';
        }

        if (isset($data['name'])) {
            $companySetting->name = $data['name'];
        }
        if (isset($data['label'])) {
            $companySetting->label = $data['label'];
        }
        if (isset($data['description'])) {
            $companySetting->description = $data['description'];
        }
        
        if (!$companySetting->save()) {
            return 'notSaved';
        }

        return $companySetting;
    }

    /**
     * Create a new CompanySetting.
     *
     * @param array $data
     *
     * @return bool|static
     */
    public function create(array $data)
    {
        $companySetting = [
            "name" => isset($data['name']) ? $data['name'] : '',
            "label" => isset($data['label']) ? $data['label'] : '',
            "description" => isset($data['description']) ? $data['description'] : '',
            "companyId" => isset($data['companyId']) ? $data['companyId'] : ''
        ];

        $companySetting = $this->model->create($companySetting);

        if (!$companySetting) {
            return false;
        }

        return $companySetting;
    }

    /**
     * Delete a CompanySetting.
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
