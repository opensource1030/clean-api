<?php


namespace WA\Services\Queues;

use Illuminate\Queue\Jobs\Job;
use WA\Helpers\Traits\SetLimits;

/**
 * Class BaseService.
 */
abstract class BaseService
{
    use SetLimits;

    /**
     * @param Job $job
     * @param $data
     *
     * @return mixed
     */
    abstract public function fire(Job $job, $data);
}
