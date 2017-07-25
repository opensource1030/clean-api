<?php

namespace WA\Http\Controllers;

use Illuminate\Http\Request;
use WA\DataStore\Content\Content;
use WA\DataStore\Content\ContentTransformer;
use WA\Repositories\Content\ContentInterface;

class ContentsController extends FilteredApiController
{
    /**
     * @var ContentInterface
     */
    protected $contents;

    /**
     * ContentsController constructor.
     *
     * @param ContentInterface $contents
     * @param Request $request
     */
    public function __construct(ContentInterface $contents, Request $request)
    {
        parent::__construct($contents, $request);
        $this->contents = $contents;
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
        if(!$this->addFilterToTheRequest("store", $request)) {
            $error['errors']['autofilter'] = Lang::get('messages.FilterErrorNotUser');
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }

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
        $data = $request->all()['data'];
        $data = $this->addRelationships($data);

        if(!$this->addFilterToTheRequest("create", $data)) {
            $error['errors']['autofilter'] = Lang::get('messages.FilterErrorNotUser');
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }
        
        $content = $this->contents->create($data['attributes']);
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
        if(!$this->addFilterToTheRequest("delete", null)) {
            $error['errors']['autofilter'] = Lang::get('messages.FilterErrorNotUser');
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }
        
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
