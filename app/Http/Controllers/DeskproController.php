<?php

namespace WA\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Deskpro\API\DeskproClient;
use Deskpro\API\Exception\APIException;

/**
 * Deskpro resource.
 *
 * @Resource("deskpro", uri="/deskpro")
 */
class DeskproController extends FilteredApiController
{
    /**
     * @var DeskproClient
     */
    protected $client;

    /**
     * DeskproController constructor.
     *
     */
    public function __construct()
    {
        $this->client = new DeskproClient($_ENV['DESKPRO_URL']);
        $this->client->setAuthKey(1, $_ENV['DEKSPRO_AUTHKEY']);
    }

    /**
     * Search Query
     *
     * @param $query
     */
    public function search(Request $request)
    {
        $query = $request->all()['query'];
        try {
            $resp = $this->client->get('/search?q=' . $query);
            return response()->json($resp->getData());
        } catch (APIException $e) {
            echo $e->getMessage();
        }
    }
}
