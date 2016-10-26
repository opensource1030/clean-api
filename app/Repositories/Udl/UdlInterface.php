<?php

namespace WA\Repositories\Udl;

interface UdlInterface
{
    /**
     * UDL by the id.
     *
     * @param int $id
     *
     * @return object object of the UDL information
     */
    public function byId($id);

    /**
     * Get the UDL information by the name.
     *
     * @param string $name
     *
     * @return object of the UDL information
     */
    public function byLabel($name);

    /**
     * Get the UDL information by the name.
     *
     * @param string $name
     * @param int    $companyId | null strongly suggested to include this as many company have the same UDL names
     *
     * @return object of the UDL information
     */
    public function byName($name, $companyId = null);

    /**
     * Get the UDL values of a UDL.
     *
     * @param int $id
     *
     * @return object object information of the UDL Values
     */
    public function byUDLValue($id);

    /**
     * Update a UDL value.
     *
     * @param int   $id
     * @param array $data
     *
     * @return bool
     */
    public function update($id, array $data);

    /**
     * Delete a UDL (soft deletes).
     *
     * @param int $id of the UDL
     *
     * @return bool
     */
    public function delete($id);

    /**
     * Create udl values for companies.
     *
     * @param string $name
     * @param int    $companyId
     * @param string $label
     *
     * @return bool
     */
    public function create($name, $companyId, $label);

    /**
     * Get UDL by Company ID.
     *
     * @param int $companyId
     *
     * @return array of UDL and Values
     */
    public function byCompanyId($companyId);
}
