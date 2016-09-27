<?php
namespace WA\Http\Controllers;

use WA\DataStore\App\App;
use WA\DataStore\App\AppTransformer;
use WA\Repositories\App\AppInterface;
use Illuminate\Http\Request;

use Log;

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
        $criteria = $this->getRequestCriteria();
        $this->app->setCriteria($criteria);
        $apps = $this->app->byPage();

        $response = $this->response()->withPaginator($apps, new AppTransformer(), ['key' => 'apps']);
        $response = $this->applyMeta($response);
        return $response;
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
        $app = App::find($id);
        if($app == null){
            $error['errors']['get'] = 'the App selected doesn\'t exists';   
            return response()->json($error)->setStatusCode(409);
        }

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
     * Delete an App
     *
     * @param $id
     */
    public function delete($id)
    {
        $app = App::find($id);
        if($app <> null){
            $this->app->deleteById($id);
        } else {
            $error['errors']['delete'] = 'the App selected doesn\'t exists';   
            return response()->json($error)->setStatusCode(409);
        }
        
        $this->index();
        $app = App::find($id);
        if($app == null){
            return array("success" => true);
        } else {
            $error['errors']['delete'] = 'the App has not been deleted';   
            return response()->json($error)->setStatusCode(409);
        }
    }
}