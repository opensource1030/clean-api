<?php

namespace WA\Repositories\Company;

use WA\Repositories\RepositoryInterface;

/**
 * Interface CompanyUserImportJobInterface.
 */
interface CompanyUserImportJobInterface extends RepositoryInterface
{

    const STATUS_PENDING = 0;
    const STATUS_WORKING = 1;
    const STATUS_SUSPENDED = 2;
    const STATUS_COMPLETED = 3;
    const STATUS_CANCELED = 4;

    /**
     * Create a new CompanyUserImportJobInterface.
     *
     * @param array $data
     *
     * @return object object of the CompanyUserImportJob | false
     */
    public function create(
        array $data
    );

    /**
     * Delete a CompanyUserImportJob.
     *
     * @param int  $id
     * @param bool $soft true soft deletes
     *
     * @return bool
     */
    public function delete($id, $soft = true);

    /**
     * Update a CompanyUserImportJob.
     *
     * @param array $data
     *
     * @return bool
     */
    public function update(array $data);

}
