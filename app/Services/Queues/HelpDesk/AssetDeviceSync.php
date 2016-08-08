<?php

namespace WA\Services\Queues\HelpDesk;

use Illuminate\Queue\Jobs\Job;
use WA\DataLoader\HelpDesk\Loader;
use WA\Services\Queues\BaseService;

/**
 * Class HelpDeskSync.
 */
class AssetDeviceSync extends BaseService
{
    protected $loader;

    /**
     * @param Loader $loader
     */
    public function __construct(Loader $loader)
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
