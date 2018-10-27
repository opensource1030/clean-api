<?php

namespace WA\Repositories\Company;

use WA\Repositories\RepositoryInterface;

/**
 * Interface CompanySettingInterface.
 */
interface CompanySettingInterface extends RepositoryInterface
{
    /**
     * Create CompanySetting.
     *
     * @param array $data
     *
     * @return bool|static
     */
    public function create(array $data);

    /**
     * Update CompanySetting.
     *
     * @param array $data
     *
     * @return bool
     */
    public function update(array $data);

    /**
     * Delete CompanySetting.
     *
     * @param int  $id
     * @param bool $soft true soft deletes
     *
     * @return bool
     */
    public function delete($id, $soft = true);
}
