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

        $page->title = $data['title'];
        $page->section = $data['section'];
        $page->active = $data['active'];
        $page->content = $data['content'];
        $page->roleId = $data['role_id'];
        $page->companyId = $data['companyId'];

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


}