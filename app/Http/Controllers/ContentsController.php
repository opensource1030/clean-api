<?php

namespace WA\Http\Controllers;

use WA\DataStore\Content\ContentTransformer;
use WA\DataStore\Content\Content;
use WA\Http\Controllers\Auth\AuthorizedController;
use WA\Repositories\Content\ContentInterface;
use Illuminate\Http\Request;

class ContentsController extends ApiController
{
    /**
     * @var ContentInterface
     */
    protected $contents;

    /**
     * Contents Controller constructor.
     *
     * @param ContentInterface $contents
     */
    public function __construct(ContentInterface $contents)
    {
        $this->contents = $contents;
    }

    /**
     * Get a payload of all available content.
     *
     * @return \Dingo\Api\Http\Response
     */
    public function index()
    {
        $criteria = $this->getRequestCriteria();
        $this->contents->setCriteria($criteria);
        $contents = $this->contents->byPage();

        $response = $this->response()->withPaginator($contents, new ContentTransformer(), ['key' => 'contents']);
        $response = $this->applyMeta($response);

        return $response;
    }

    /**
     * Get a payload of content by single id.
     *
     * @param $id
     *
     * @return \Dingo\Api\Http\Response
     */
    public function show($id)
    {
        $criteria = $this->getRequestCriteria();
        $this->contents->setCriteria($criteria);
        $content = $this->contents->byId($id);

        if (!isset($content)) {
            $error['errors']['put'] = 'Content selected does not exist';
            return response()->json($error)->setStatusCode(404);
        }

        return $this->response()->item($content, new ContentTransformer(), ['key' => 'contents']);
    }

    /**
     * Update contents.
     *
     * @param $id
     *
     * @return \Dingo\Api\Http\Response
     */
    public function store($id, Request $request)
    {
        $content = Content::find($id);
        if (!isset($content)) {
            $error['errors']['put'] = 'Content selected does not exist';
            return response()->json($error)->setStatusCode(404);
        }
        $data = $request->all();
        $data['id'] = $id;
        $content = $this->contents->update($data);
        if (!$content) {
            $error['errors']['put'] = 'Content could not be updated. Please check your data';
            return response()->json($error)->setStatusCode(403);
        }

        return $this->response()->item($content, new ContentTransformer(), ['key' => 'contents']);
    }

    /**
     * Create new content.
     *
     * @return \Dingo\Api\Http\Response
     */
    public function create(Request $request)
    {
        $data = $request->all();
        $content = $this->contents->create($data);
        if (!$content) {
            $error['errors']['post'] = 'Content could not be created. Please check your data';
            return response()->json($error)->setStatusCode(403);
        }

        return $this->response()->item($content, new ContentTransformer(), ['key' => 'contents']);
    }

    /**
     * Delete content.
     *
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteContent($id)
    {
        $content = Content::find($id);
        if (!isset($content)) {
            $error['errors']['delete'] = 'Content selected does not exist';
            return response()->json($error)->setStatusCode(404);
        }

        $this->contents->deleteById($id);
        $content = Content::find($id);
        if (!$content) {
            return response()->json()->setStatusCode(204);
        } else {
            return response()->json()->setStatusCode(202);
        }
    }
}
