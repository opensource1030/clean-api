<?php

namespace WA\Http\Controllers;

use Illuminate\Http\Request;
use WA\DataStore\App\App;
use WA\DataStore\App\AppTransformer;
use WA\Repositories\App\AppInterface;
use Illuminate\Support\Facades\Lang;

/**
 * App resource.
 *
 * @Resource("app", uri="/apps")
 */
class AppsController extends ApiController
{
    /**
     * @var AppInterface
     */
    protected $app;

    /**
     * App Controller constructor.
     *
     * @param AppInterface $app
     */
    public function __construct(AppInterface $app)
    {
        $this->app = $app;
    }

    /**
     * Show all App.
     *
     * Get a payload of all App
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
     * Show a single App.
     *
     * Get a payload of a single App
     *
     * @Get("/{id}")
     */
    public function show($id, Request $request)
    {
        $criteria = $this->getRequestCriteria();
        $this->app->setCriteria($criteria);
        $app = App::find($id);

        if ($app == null) {
            $error['errors']['get'] = Lang::get('messages.NotExistClass', ['class' => 'App']);

            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }

        return $this->response()->item($app, new AppTransformer(), ['key' => 'apps'])->setStatusCode($this->status_codes['created']);
    }

    /**
     * Update contents of a app.
     *
     * @param $id
     *
     * @return \Dingo\Api\Http\Response
     */
    public function store($id, Request $request)
    {
        if ($this->isJsonCorrect($request, 'apps')) {
            try {
                $data = $request->all()['data']['attributes'];
                $data['id'] = $id;
                $app = $this->app->update($data);

                if ($app == 'notExist') {
                    $error['errors']['app'] = Lang::get('messages.NotExistClass', ['class' => 'App']);
                    //$error['errors']['Message'] = $e->getMessage();
                    return response()->json($error)->setStatusCode($this->status_codes['notexists']);
                }

                if ($app == 'notSaved') {
                    $error['errors']['app'] = Lang::get('messages.NotSavedClass', ['class' => 'App']);
                    //$error['errors']['devicesMessage'] = $e->getMessage();
                    return response()->json($error)->setStatusCode($this->status_codes['conflict']);
                }

                return $this->response()->item($app, new AppTransformer(), ['key' => 'apps'])->setStatusCode($this->status_codes['created']);
            } catch (\Exception $e) {
                $error['errors']['apps'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'App', 'option' => 'updated', 'include' => '']);
                //$error['errors']['Message'] = $e->getMessage();
            }
        } else {
            $error['errors']['json'] = Lang::get('messages.InvalidJson');
        }

        return response()->json($error)->setStatusCode($this->status_codes['conflict']);
    }

    /**
     * Create a new App.
     *
     * @return \Dingo\Api\Http\Response
     */
    public function create(Request $request)
    {
        if ($this->isJsonCorrect($request, 'apps')) {
            try {
                $data = $request->all()['data']['attributes'];
                $app = $this->app->create($data);

                return $this->response()->item($app, new AppTransformer(), ['key' => 'apps'])->setStatusCode($this->status_codes['created']);
            } catch (\Exception $e) {
                $error['errors']['apps'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'App', 'option' => 'created', 'include' => '']);
                //$error['errors']['Message'] = $e->getMessage();
            }
        } else {
            $error['errors']['json'] = Lang::get('messages.InvalidJson');
        }

        return response()->json($error)->setStatusCode($this->status_codes['conflict']);
    }

    /**
     * Delete an App.
     *
     * @param $id
     */
    public function delete($id)
    {
        $app = App::find($id);
        if ($app != null) {
            $this->app->deleteById($id);
        } else {
            $error['errors']['delete'] = Lang::get('messages.NotExistClass', ['class' => 'App']);

            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }

        $app = App::find($id);
        if ($app == null) {
            return array('success' => true);
        } else {
            $error['errors']['delete'] = Lang::get('messages.NotDeletedClass', ['class' => 'App']);

            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }
    }
}
