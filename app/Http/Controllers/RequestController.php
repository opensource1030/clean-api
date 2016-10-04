<?php
namespace WA\Http\Controllers;

use Illuminate\Http\Request;
use WA\DataStore\Request\RequestTransformer;
use WA\Repositories\Request\RequestInterface;

/**
 * Request resource.
 *
 * @Resource("Request", uri="/Request")
 */
class RequestController extends ApiController
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * Request Controller constructor
     *
     * @param RequestInterface $Request
     */
    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * Show all Request
     *
     * Get a payload of all Request
     *
     */
    public function index()
    {
        $criteria = $this->getRequestCriteria();
        $this->request->setCriteria($criteria);

        $request = $this->request->byPage();

        $response = $this->response()->collection($request, new RequestTransformer(), ['key' => 'requests']);
        $response = $this->applyMeta($response);
        return $response;
    }

    /**
     * Show a single Request
     *
     * Get a payload of a single Request
     *
     * @Get("/{id}")
     */
    public function show($id)
    {
        $criteria = $this->getRequestCriteria();
        $this->request->setCriteria($criteria);

        $request = $this->request->byId($id);
        return $this->response()->item($request, new RequestTransformer(), ['key' => 'requests']);
    }

    /**
     * Update contents of a Request
     *
     * @param $id
     * @return \Dingo\Api\Http\Response
     */
    public function store($id, Request $request)
    {
        $data = $request->all();
        $data['id'] = $id;
        $request = $this->request->update($data);
        return $this->response()->item($request, new RequestTransformer(), ['key' => 'requests']);
    }

    /**
     * Create a new Request
     *
     * @return \Dingo\Api\Http\Response
     */
    public function create(Request $request)
    {
        $data = $request->all();
        $request = $this->request->create($data);
        return $this->response()->item($request, new RequestTransformer(), ['key' => 'requests']);
    }

    /**
     * Delete a Request
     *
     * @param $id
     */
    public function delete($id)
    {
        $this->request->deleteById($id);
        $this->index();
    }
}
