<?php

namespace WA\Services\Queues\HelpDesk;

use Illuminate\Queue\Jobs\Job;
use WA\DataLoader\HelpDesk\Loader;
use WA\Services\Queues\BaseService;
use WA\DataLoader\HelpDesk\Loader\EmployeeLoader;

/**
 * Class HelpDeskSync.
 */
class EmployeeSync extends BaseService
{
    protected $loader;

    /**
     * HelpDeskSync constructor.
     * @param EmployeeLoader $loader
     */
    public function __construct(EmployeeLoader $loader)
    {
        $this->loader = $loader;
    }

    /**
     * @param Job $job
     * @param $data
     *
     * @return mixed
     */
    public function fire(Job $job, $data)
    {
        $this->loader->run();
        $job->delete();
    }
}
