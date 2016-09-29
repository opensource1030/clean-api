<?php

namespace WA\Http\Controllers;

use Illuminate\Http\Request;

use WA\DataStore\App\App;
use WA\DataStore\App\AppTransformer;
use WA\Repositories\App\AppInterface;

use Auth;

/**
 * App resource.
 *
 * @Resource("app", uri="/apps")
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
    public function __construct(AppInterface $app) {
        
        $this->app = $app;
    }

    /**
     * Show all App
     *
     * Get a payload of all App
     *
     */
    public function index() {

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
    public function show($id) {

        //Auth::loggedIn();
        //dd(Auth::check());
        //$auth = new Auth();
        //dd($auth);
        //$user = $auth->user();
        //$user = Auth::user();
        //dd($user);

        $app = App::find($id);
        if($app == null){
            $error['errors']['get'] = 'the App selected doesn\'t exists';   
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }

        // Dingo\Api\src\Http\Response\Factory.php
        // Dingo\Api\src\Http\Transformer\Factory.php

        return $this->response()->item($app, new AppTransformer(),['key' => 'apps'])->setStatusCode($this->status_codes['created']);
    }

    /**
     * Update contents of a app
     *
     * @param $id
     * @return \Dingo\Api\Http\Response
     */
    public function store($id, Request $request) {

        if($this->isJsonCorrect($request, 'apps')){
            try {
                $data = $request->all()['data']['attributes'];
                $data['id'] = $id;
                $app = $this->app->update($data);
                return $this->response()->item($app, new AppTransformer(), ['key' => 'apps'])->setStatusCode($this->status_codes['created']);
            } catch (\Exception $e){
                $error['errors']['apps'] = 'the App has not been updated';
                //$error['errors']['appsMessage'] = $e->getMessage();
            }
        } else {
            $error['errors']['json'] = 'Json is Invalid';
        }

        return response()->json($error)->setStatusCode($this->status_codes['conflict']);
    }

    /**
     * Create a new App
     *
     * @return \Dingo\Api\Http\Response
     */
    public function create(Request $request) {

        if($this->isJsonCorrect($request, 'apps')){
            try {
                $data = $request->all()['data']['attributes'];
                $app = $this->app->create($data);
                return $this->response()->item($app, new AppTransformer(), ['key' => 'apps']);
            } catch (\Exception $e){
                $error['errors']['apps'] = 'the App has not been created';
                //$error['errors']['appsMessage'] = $e->getMessage();
            }
        } else {
            $error['errors']['json'] = 'Json is Invalid';
        }

        return response()->json($error)->setStatusCode($this->status_codes['conflict']);
    }

    /**
     * Delete an App
     *
     * @param $id
     */
    public function delete($id) {

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