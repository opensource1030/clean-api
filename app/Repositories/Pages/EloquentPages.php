<?php

namespace WA\Repositories\Pages;

use WA\Repositories\AbstractRepository;

/**
 * Class EloquentPages
 *
 * @package WA\Repositories\Pages
 */
class EloquentPages extends AbstractRepository implements PagesInterface
{


    /**
     * Update pages
     *
     * @param array $data
     * @return bool
     */
    public function update(array $data)
    {
        $page = $this->model->find($data['id']);

        if(!$page)
        {
            return false;
        }

        $page->title =  isset($data['title']) ? $data['title'] : null ;
        $page->section = isset($data['section']) ? $data['section'] : null;
        $page->active = isset($data['active']) ? $data['active'] : 0;
        $page->content = isset($data['content']) ? $data['content'] : null;
        $page->owner_type = isset($data['owner_type']) ? $data['owner_type'] : null;
        $page->owner_id = isset($data['owner_id']) ? $data['owner_id'] : 0 ;

        if(!$page->save()) {
            return false;
        }

        return $page;

    }

    /**
     * Get the company Id tied to the page
     *
     * @param $id
     * @return mixed
     */
    public function getCompanyId($id)
    {
        return $this->model->where('id', $id)->first()->companies;
    }

    /**
     * Get an array of all the available pages.
     *
     * @return Array of pages
     */
    public function getAllPages()
    {
        $pages =  $this->model->all();
        return $pages;
    }

    /**
     * Create a new page
     *
     * @param array $data
     * @return bool|static
     */
    public function create(array $data)
    {

        $pageData = [
        "title" =>  isset($data['title']) ? $data['title'] : null ,
        "section" => isset($data['section']) ? $data['section'] : null,
        "active" => !empty($data['active']) ? 1 : 0,
        "content" => isset($data['content']) ? $data['content'] : null,
        "owner_type" => isset($data['owner_type']) ? $data['owner_type'] : null,
        "owner_id" => isset($data['owner_id']) ? $data['owner_id'] : 0,
        ];

        $page = $this->model->create($pageData);

        if(!$page) {
            return false;
        }

        return $page;
    }

    /**
     * Delete a Page.
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