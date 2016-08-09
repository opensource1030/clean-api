<?php

namespace WA\Services\Queues\HelpDesk;

use Illuminate\Queue\Jobs\Job;
use WA\DataLoader\HelpDesk\Loader;
use WA\Services\Queues\BaseService;
use WA\DataLoader\HelpDesk\Loader\UserLoader;

/**
 * Class HelpDeskSync.
 */
class UserSync extends BaseService
{
    protected $loader;

    /**
     * HelpDeskSync constructor.
     * @param UserLoader $loader
     */
    public function __construct(UserLoader $loader)
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
