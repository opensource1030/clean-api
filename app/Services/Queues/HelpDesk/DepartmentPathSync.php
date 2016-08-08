<?php

namespace WA\Services\Queues\HelpDesk;

use Illuminate\Queue\Jobs\Job;
use WA\DataLoader\HelpDesk\Loader;
use WA\Services\Queues\BaseService;
use WA\DataLoader\HelpDesk\Loader\DepartmentPathLoader;

/**
 * Class DepartmentPathSync.
 */
class DepartmentPathSync extends BaseService
{
    protected $loader;

    /**
     * DepartmentPathSync constructor.
     * @param DepartmentPathLoader $loader
     */
    public function __construct(DepartmentPathLoader $loader)
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
