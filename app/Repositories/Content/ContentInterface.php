<?php

namespace WA\Repositories\Content;

use WA\Repositories\RepositoryInterface;

/**
 * Interface ContentInterface
 *
 * @package WA\Repositories\Content
 */
interface ContentInterface extends RepositoryInterface
{
    /**
     * Update content.
     *
     * @param array $data
     *
     * @return bool
     */
    public function update(array $data);

    /**
     * Get the company Id tied to content
     *
     * @param $id
     * @return mixed
     */
    public function getCompanyId($id);

    /**
     * Get an array of all the available content.
     *
     * @return Array of contents
     */
    public function getAllContents();

    /**
     * Create new content
     *
     * @param array $data
     * @return bool|static
     */
    public function create(array $data);

    /**
     * Delete Content.
     *
     * @param int  $id
     * @param bool $soft true soft deletes
     *
     * @return bool
     */
    public function delete($id, $soft = true);

}