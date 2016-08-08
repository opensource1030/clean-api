<?php

namespace WA\Http\Controllers\Admin;

use WA\Http\Controllers\BaseController;
use WA\Http\Requests\Request;

/**
 * Allows for Creating and Editing all the data mappings in the system.
 *
 *
 * Class RawDataController
 */
class RawDataController extends BaseController
{
    public function index()
    {
        return view('mappers.index');
    }

    public function routeToMapper(Request $request)
    {
        // routes to any available interface mapper
        switch ($request->input('type')) {

            case 'census':
                return redirect()->route('mapper.census.index');
                break;

        }

        return redirect()->route('mapper.index');
    }
}
