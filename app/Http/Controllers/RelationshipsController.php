<?php

namespace WA\Http\Controllers;

use Illuminate\Support\Facades\Lang;
use WA\DataStore\Relationship\RelationshipTransformer;

/**
 * App resource.
 *
 * @Resource("app", uri="/apps")
 */
class RelationshipsController extends ApiController
{
    public function includeRelationships($modelPlural, $id, $includePlural)
    {
        $modelName = title_case($modelPlural);
        $modelSingular = str_singular($modelName);
        $controllerName = "\\WA\\Http\\Controllers\\${modelName}Controller";

        if (!class_exists($controllerName)) {
            $error['errors'][$modelPlural] = \Illuminate\Support\Facades\Lang::get('messages.NotExistClass',
                  ['class' => $modelSingular]);
            return response()->json($error)->setStatusCode(404);
        }

        $controller = app()->make($controllerName);

        $criteria = $controller->getRequestCriteria();
        $plural = str_plural($modelPlural);

        if ($plural == $modelPlural) {
            $model = title_case(str_singular($modelPlural));
        } else {
            // NOT EXISTS MODEL ( SINGULAR INPUT )
            $error['errors'][$modelPlural] = Lang::get('messages.NotExistClass', ['class' => $modelPlural]);

            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }

        try {
            $class = "\\WA\\DataStore\\$model\\$model";
            if (class_exists($class) && !($class::find($id) == null)) {
                $results = $class::find($id)->{$includePlural}()->paginate(25);
            } else {
                // NOT EXISTS MODEL ( NOT IN DATASTORE )
                $error['errors'][$modelPlural] = Lang::get('messages.NotExistClass', ['class' => $model]);

                return response()->json($error)->setStatusCode($this->status_codes['notexists']);
            }
        } catch (\Exception $e) {
            // NOT EXISTS INCLUDE ( NOT IN DATASTORE )
            //$error['errors']['Message'] = $e->getMessage();
            $error['errors'][$modelPlural] = Lang::get('messages.NotExistClass', ['class' => $includePlural]);

            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }

        if ($results == null) {
            // NOT EXISTS INCLUDE ( NO DATA )
            $error['errors']['getIncludes'] = Lang::get('messages.NotExistInclude');

            return response()->json($error)->setStatusCode($this->status_codes['badrequest']);
        }

        $response = $this->response()->withPaginator($results, new RelationshipTransformer(), ['key' => $includePlural]);
        $response = $this->applyMeta($response);

        return $response;
    }

    public function includeInformationRelationships($modelPlural, $id, $includePlural)
    {
        $modelName = title_case($modelPlural);
        $modelSingular = str_singular($modelName);
        $controllerName = "\\WA\\Http\\Controllers\\${modelName}Controller";

        if (!class_exists($controllerName)) {
            $error['errors'][$modelPlural] = \Illuminate\Support\Facades\Lang::get('messages.NotExistClass',
                  ['class' => $modelSingular]);
            return response()->json($error)->setStatusCode(404);
        }

        $controller = app()->make($controllerName);

        $criteria = $controller->getRequestCriteria();
        $plural = str_plural($modelPlural);

        if ($plural == $modelPlural) {
            $model = title_case(str_singular($modelPlural));
        } else {
            // NOT EXISTS MODEL ( SINGULAR INPUT )
            $error['errors'][$modelPlural] = Lang::get('messages.NotExistClass', ['class' => $modelPlural]);

            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }

        try {
            $class = "\\WA\\DataStore\\$model\\$model";
            if (class_exists($class)) {
                $results = $class::find($id)->{$includePlural}()->paginate(25);
            } else {
                // NOT EXISTS MODEL ( NOT IN DATASTORE )
                $error['errors'][$modelPlural] = Lang::get('messages.NotExistClass', ['class' => $model]);

                return response()->json($error)->setStatusCode($this->status_codes['notexists']);
            }
        } catch (\Exception $e) {
            // NOT EXISTS INCLUDE ( NOT IN DATASTORE )
            //$error['errors']['Message'] = $e->getMessage();
            $error['errors'][$modelPlural] = Lang::get('messages.NotExistClass', ['class' => $includePlural]);

            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }

        if ($results == null) {
            // NOT EXISTS INCLUDE ( NO DATA )
            $error['errors']['getIncludes'] = Lang::get('messages.NotExistInclude');

            return response()->json($error)->setStatusCode($this->status_codes['badrequest']);
        }

        $transformer = $this->createTransformer($includePlural);

        $response = $this->response()->withPaginator($results, new $transformer(), ['key' => $includePlural]);
        $response = $this->applyMeta($response);

        return $response;
    }
}
