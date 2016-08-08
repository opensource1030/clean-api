<?php

namespace WA\Repositories\Census;

use WA\Repositories\RepositoryInterface;

interface CensusInterface extends RepositoryInterface

{
    /**
     * Get the census by the ID.
     *
     * @param int $id
     *
     * @return Object of the census information
     */
    public function byId($id);

    /**
     * Get paginated census.
     *
     * @param int  $page
     * @param      $limit
     * @param bool $all
     *
     * @return Object of census objects
     */
    public function byPage($page = 1, $limit = 10, $all = false);

    /**
     * Update a company's census status
     *
     *
     * @param int    $id     of the census
     * @param int    $companyId
     * @param string $status {loaded | suspended | failed | complete}
     * @param array $options other optional fields
     *
     * @return bool
     */
    public function update($id, $companyId, $status, $options = []);

    /**
     * Get the census by the company information.
     *
     * @param int  $companyId
     * @param bool $last     the most recently updated
     * @param int  $limit    amount to return
     * @param bool $complete census that is completed
     *
     * @return Object of census information by company
     */
    public function byCompany($companyId, $last = false, $limit = 5, $complete = true);

    /**
     * Get the Employee Count by the Census ID
     *
     * @param int $id
     *
     * @return int count
     */
    public function getEmployeeCountById($id);

    /**
     * @param int $id
     *
     * @return Object of  logs
     */
    public function getLogs($id);

    /**
     * Gets the count on by Census ID
     *
     * @param int $id
     * @return int
     */
    public function getLogsCount($id);

}
