<?php
namespace WA\Http\Controllers\Api;

use Auth;
use WA\DataStore\Allocation\AllocationTransformer;
use WA\Repositories\Allocation\AllocationInterface;
use WA\Repositories\User\UserInterface;
use WA\Services\ApiHandler\SQL\ApiHandler as SQLApiHandler;

/**
 * Allocations resource.
 *
 * @Resource("Allocations", uri="/allocations")
 */
class AllocationsController extends ApiController
{
    /**
     * @var AllocationInterface
     */
    protected $allocations;

    /**
     * @var UserInterface
     */
    protected $user;

    /**
     * AllocationsController constructor.
     *
     * @param AllocationInterface $allocations
     * @param UserInterface   $user
     */
    public function __construct(AllocationInterface $allocations, UserInterface $user)
    {
        $this->allocations = $allocations;
        $this->employee = $user;
    }

    /**
     * Show all allocations
     *
     * Get a payload of all allocations
     *
     * @Get("/")
     * @Parameters({
     *      @Parameter("page", description="The page of results to view.", default=1),
     *      @Parameter("limit", description="The amount of results per page.", default=10),
     *      @Parameter("access_token", required=true, description="Access token for authentication")
     * })
     */
    public function index()
    {
        $allocations = $this->allocations->byPage();

        return $this->response()->withPaginator($allocations, new AllocationTransformer(),['key' => 'allocations']);
    }

    /**
     * Show a single allocation
     *
     * Get a payload of a single allocation
     *
     * @Get("/{id}")
     */
    public function show($id)
    {
        $allocation = $this->allocations->byId($id);

        return $this->response()->item($allocation, new AllocationTransformer(),['key' => 'allocations']);
    }

}