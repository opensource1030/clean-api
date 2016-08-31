<?php
namespace WA\Http\Controllers;

use App;
use WA\DataStore\App\AppTransformer;
use WA\Repositories\App\AppInterface;
use Illuminate\Http\Request;

/**
 * app resource.
 *
 * @Resource("App", uri="/app")
 */
class AppController extends ApiController
{
    /**
     * @var AppInterface
     */
    protected $app;

    /**
     * App Controller constructor
     *
     * @param AppInterface $app
     */
    public function __construct(AppInterface $app)
    {
        $this->app = $app;
    }

    /**
     * Show all App
     *
     * Get a payload of all App
     *
     */
    public function index()
    {
        $app = $this->app->byPage();
        return $this->response()->withPaginator($app, new AppTransformer(),['key' => 'apps']);

    }

    /**
     * Show a single App
     *
     * Get a payload of a single App
     *
     * @Get("/{id}")
     */
    public function show($id)
    {
        $app = $this->app->byId($id);
        return $this->response()->item($app, new AppTransformer(), ['key' => 'apps']);
    }

    /**
     * Update contents of a app
     *
     * @param $id
     * @return \Dingo\Api\Http\Response
     */
    public function store($id, Request $request)   
    {
        $data = $request->all();       
        $data['id'] = $id;
        $app = $this->app->update($data);
        return $this->response()->item($app, new AppTransformer(), ['key' => 'apps']);
    }

    /**
     * Create a new App
     *
     * @return \Dingo\Api\Http\Response
     */
    public function create(Request $request)
    {
        $data = $request->all();
        $app = $this->app->create($data);
        return $this->response()->item($app, new AppTransformer(), ['key' => 'apps']);
    }

    /**
     * Delete a App
     *
     * @param $id
     */
    public function delete($id)
    {
        $this->app->deleteById($id);
        $this->index();
    }
}