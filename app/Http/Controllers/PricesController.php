<?php

namespace WA\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use WA\DataStore\Price\Price;
use WA\DataStore\Price\PriceTransformer;
use WA\Repositories\Price\PriceInterface;

/**
 * Price resource.
 *
 * @Resource("Price", uri="/Price")
 */
class PricesController extends FilteredApiController
{
    /**
     * @var PriceInterface
     */
    protected $price;

    /**
     * PricesController constructor.
     *
     * @param PriceInterface $price
     * @param Request $request
     */
    public function __construct(PriceInterface $price, Request $request)
    {
        parent::__construct($price, $request);
        $this->price = $price;
    }

    /**
     * Update contents of a Price.
     *
     * @param $id
     *
     * @return \Dingo\Api\Http\Response
     */
    public function store($id, Request $request)
    {
        /*
         * Checks if Json has data, data-type & data-attributes.
         */
        if (!$this->isJsonCorrect($request, 'prices')) {
            $error['errors']['json'] = Lang::get('messages.InvalidJson');

            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }

        $data = $request->all()['data']['attributes'];
        $data['id'] = $id;
        $price = $this->price->update($data);

        if ($price == 'notExist') {
            $error['errors']['price'] = Lang::get('messages.NotExistClass', ['class' => 'Price']);
            //$error['errors']['Message'] = $e->getMessage();
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }

        if ($price == 'notSaved') {
            $error['errors']['price'] = Lang::get('messages.NotSavedClass', ['class' => 'Price']);
            //$error['errors']['Message'] = $e->getMessage();
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }

        return $this->response()->item($price, new PriceTransformer(),
            ['key' => 'prices'])->setStatusCode($this->status_codes['created']);
    }

    /**
     * Create a new Price.
     *
     * @return \Dingo\Api\Http\Response
     */
    public function create(Request $request)
    {
        if (!$this->isJsonCorrect($request, 'prices')) {
            $error['errors']['json'] = Lang::get('messages.InvalidJson');

            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }

        $data = $request->all()['data']['attributes'];
        $price = $this->price->create($data);

        return $this->response()->item($price, new PriceTransformer(),
            ['key' => 'prices'])->setStatusCode($this->status_codes['created']);
    }

    /**
     * Delete a Price.
     *
     * @param $id
     */
    public function delete($id)
    {
        $price = Price::find($id);
        if ($price != null) {
            $this->price->deleteById($id);
        } else {
            $error['errors']['delete'] = Lang::get('messages.NotExistClass', ['class' => 'Price']);
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }


        $price = Price::find($id);
        if ($price == null) {
            return array("success" => true);
        } else {
            $error['errors']['delete'] = Lang::get('messages.NotDeletedClass', ['class' => 'Price']);
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }
    }
}
