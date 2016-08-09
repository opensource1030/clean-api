<?php

namespace WA\Repositories\Census;

use Illuminate\Database\Eloquent\Model;
use WA\Repositories\AbstractRepository;
use WA\Repositories\JobStatus\JobStatusInterface;

use Log;

class EloquentCensus extends AbstractRepository implements CensusInterface
{
    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * @var \WA\Repositories\JobStatus\JobStatusInterface
     */
    protected $jobStatus;

    public function __construct(Model $model, JobStatusInterface $jobStatus)
    {
        $this->model = $model;

        $this->jobStatus = $jobStatus;
    }

    /**
     * Get the census by the ID.
     *
     * @param int $id
     *
     * @return Object of the census information
     */
    public function byId($id)
    {
        return $this->model->find($id);
    }

    /**
     * Get paginated census.
     *
     * @param int $page
     * @param     $limit
     *
     * @return Object as collection of paginated objects
     */
    public function byPage($page = 1, $limit = 10, $all = false)
    {
        $result = new \StdClass();
        $result->page = $page;
        $result->limit = $limit;
        $result->totalItems = 0;
        $result->items = [];

        $censuses = $this->model;

        if ($all) {
            $result->items = $censuses->all();
        } else {
            $result->items =
                $censuses->skip($limit * ($page - 1))
                    ->take($limit)
                    ->orderBy('updated_at', 'DESC')
                    ->get();
        }

        $result->totalItems = $this->model->count();

        return $result;
    }

    /**
     * Update a company's census status.
     *
     *
     * @param int    $id     of the census
     * @param int    $companyId
     * @param string $status {loaded | suspended | failed | complete}
     * @param array  $options
     *
     * @return bool
     */
    public function update($id, $companyId, $status, $options = [])
    {
        $statusId = $this->jobStatus->idByName($status);
        $census = $this->byId($id);

        $data = [
            'companyId' => $companyId,
            'statusId' => $statusId,
        ];

        if (!empty($options)) {
            $data = array_merge($data, $options);
        }

        return $census->update($data);
    }

    /**
     * Get the census by the company information.
     *
     * @param int  $companyId
     * @param bool $last     the most recently updated
     * @param int  $limit    amount to return
     * @param bool $complete census that is completed
     *
     * @return Object of census information by company
     */
    public function byCompany($companyId, $last = false, $limit = 5, $complete = true)
    {
        $census = $this->model->where('companyId', $companyId);

        if ($complete) {
            $completeId = $this->jobStatus->idByName('complete');
            $census->where('statusId', $completeId);
        }

        if ($last) {
            return $census->orderBy('created_at', 'DESC')->first();
        }


        return $census->orderBy('created_at', 'DESC')->take($limit)->get();
    }

    /**
     * @param $id
     *
     * @return array
     */
    public function getLogs($id)
    {
        $census = $this->byId($id);

        $logs = $census->logs()
            ->where('syncId', $id)
            ->get();

        return $logs;
    }

    /**
     * Get the User Count by the Census ID
     *
     * @param int $id
     *
     * @return int count
     */
    public function getUserCountById($id)
    {
        $census = $this->byId($id);

        $count = $census->users()->select(
            \DB::raw('count(*) as count'))
            ->where('syncId', $id)
            ->pluck('count');

        return (int)$count;

    }

    /**
     * Gets the count on by Census ID
     *
     * @param int $id
     *
     * @return int
     */
    public function getLogsCount($id)
    {
        $census = $this->byId($id);

        $count = $census->logs()->select(
            \DB::raw('count(*) as count'))
            ->where('syncId', $id)
            ->groupBy('message')
            ->pluck('count');

        return (int)$count;
    }


}
