<?php

namespace WA\Repositories\Pages;

use WA\Repositories\RepositoryInterface;

/**
 * Interface PagesInterface
 *
 * @package WA\Repositories\Pages
 */
interface PagesInterface extends RepositoryInterface
{
    /**
     * Update a page.
     *
     * @param array $data
     *
     * @return bool
     */
    public function update(array $data);

    /**
     * Get the company Id tied to the page
     *
     * @param $id
     * @return mixed
     */
    public function getCompanyId($id);

    /**
     * Get an array of all the available pages.
     *
     * @return Array of pages
     */
    public function getAllPages();

    /**
     * Create a new page
     *
     * @param array $data
     * @return bool|static
     */
    public function create(array $data);

    /**
     * Delete a Page.
     *
     * @param int  $id
     * @param bool $soft true soft deletes
     *
     * @return bool
     */
    public function delete($id, $soft = true);

}