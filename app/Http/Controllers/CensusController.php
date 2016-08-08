<?php

namespace WA\Http\Controllers\Api;

use Cartalyst\DataGrid\Laravel\Facades\DataGrid;
use WA\Repositories\Census\CensusInterface;

/**
 * Class CompaniesController.
 */
class CensusController extends ApiController
{

    /**
     * @var CensusInterface
     */
    protected $census;

    /**
     * CensusController constructor.
     *
     * @param CensusInterface $census
     */
    public function __construct(CensusInterface $census)
    {
        $this->census = $census;
    }


    /**
     * Handles the datatables, this needs to be in a specific format to make it compatible
     * with the DataTale
     * ! overrides the default (dingo/api)
     * Returns all companies
     *
     * @return DataGrid
     */
    public function getLogDataTable($id)
    {
        $logs = $this->census->getLogs($id, false);

        $columns = [
            'identification',
            'email',
            'firstName',
            'lastName',
            'message',
        ];

        $options = [
            'throttle' => $this->defaultQueryParams['_perPage'],
        ];

        $response = DataGrid::make($logs, $columns, $options);

        return $response;
    }

}
