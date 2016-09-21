<?php

namespace WA\Http\Controllers;

use Illuminate\Routing\Controller;
use View;

/**
 * Class BaseController.
 */
class BaseController extends Controller
{
    protected $notifyContainer = 'clean';

    protected $view;

    protected $data = [];

    /**
     * Setup the layout used by the controller.
     */
    protected function setupLayout()
    {
        if (!is_null($this->layout)) {
            $this->layout = View::make($this->layout);
        }
    }

    /*
     *      Checks if a JSON param has "data", "type" and "attributes" keys and "type" is equal to "devices".
     *
     *      @param: 
     *          "data" : {
     *              "type" : "devices",
     *              "attributes" : {
     *              ...
     *      @return:
     *          boolean;
     */
    public function isJsonCorrect($request, $type){

        if(!isset($request['data'])){ 
            dd("1");
            return false;
        } else {
            $data = $request['data'];    
            if(!isset($data['type'])){
                dd("2");
                return false; 
            } else {
                if($data['type'] <> $type){
                    dd("3");
                    return false; 
                } 
            }
            if(!isset($data['attributes'])){ 
                dd("4");
                return false; 
            }
        }
        return true;
    }
}
