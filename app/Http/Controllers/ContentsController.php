<?php

namespace WA\Http\Controllers;


use WA\DataStore\Content\ContentTransformer;
use WA\Http\Controllers\Auth\AuthorizedController;
use WA\Repositories\Content\ContentInterface;
use Illuminate\Http\Request;

class ContentsController extends AuthorizedController
{
    /**
     * @var ContentInterface
     */
    protected $contents;

    /**
     * Contents Controller constructor
     *
     * @param ContentInterface $contents
     */
    public function __construct(ContentInterface $contents)
    {
        $this->contents = $contents;
    }

    /**
     * Get a payload of all available content
     *
     * @return \Dingo\Api\Http\Response
     */
    public function index()
    {
        $contents = $this->contents->getAllContents();
        return $this->response()->collection($contents, new ContentTransformer(), ['key' => 'contents']);
    }

    /**
     * Get a payload of content by single id
     *
     * @param $id
     * @return \Dingo\Api\Http\Response
     */
    public function show($id)
    {
        $content = $this->contents->byId($id);
        return $this->response()->item($content, new ContentTransformer(), ['key' => 'contents']);
    }

    /**
     * Update contents
     *
     * @param $id
     * @return \Dingo\Api\Http\Response
     */
    public function store($id, Request $request)
    {
        $data = $request->all();
        $data['id'] = $id;
        $content = $this->contents->update($data);
        return $this->response()->item($content, new ContentTransformer(), ['key' => 'contents']);
    }

    /**
     * Create new content
     *
     * @return \Dingo\Api\Http\Response
     */
    public function create(Request $request)
    {
        $data = $request->all();
        $content = $this->contents->create($data);
        return $this->response()->item($content, new ContentTransformer(), ['key' => 'contents']);
    }

    /**
     * Delete content
     *
     * @param $id
     */
    public function deleteContent($id)
    {
        $this->contents->deleteById($id);
        $this->index();
    }






}