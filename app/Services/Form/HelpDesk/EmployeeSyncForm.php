<?php

namespace WA\Services\Form\HelpDesk;

use Illuminate\Queue\QueueManager;
use WA\DataLoader\HelpDesk\Loader;
use WA\Repositories\SyncJob\SyncJobInterface;
use WA\Services\Form\AbstractForm;
use WA\DataLoader\HelpDesk\Loader\EmployeeLoader;
/**
 * Class SyncForm.
 */
class EmployeeSyncForm extends AbstractForm
{
    /**
     * @var Loader
     */
    protected $loader;

    /**
     * @var QueueManager
     */
    protected $queue;

    /**
     * @var SyncJobInterface
     */
    protected $sync;

    protected $queueRunner = 'WA\Services\Queues\HelpDesk\EmployeeSync';

    /**
     * EmployeeSyncForm constructor.
     *s
     * @param EmployeeLoader $loader
     * @param QueueManager $queue
     * @param SyncJobInterface $sync
     */
    public function __construct(EmployeeLoader $loader, QueueManager $queue, SyncJobInterface $sync)
    {
        $this->loader = $loader;
        $this->queue = $queue;
        $this->sync = $sync;
    }

    /**
     * @return bool
     */
    public function runLoader()
    {
        if (!$this->queue->push($this->queueRunner)) {
            $this->notify('error', 'There was a problem loading the queue, please try later');

            return false;
        }

        $this->notify('success', 'The job is being processed. Refresh this page to view status');

        return true;
    }

    /**
     * @param int    $limit
     * @param string $name
     * @param string $status (Sync Complete, Sync Pending, Sync Failed)
     *
     * @return Object
     */
    public function getSyncs($limit, $name = 'help-desk', $status = 'Sync Complete')
    {
        return $this->sync->byName($name, $status, $limit);
    }

    /**
     * Checks if the files are ready to be loaded.
     */
    public function isReady()
    {
        return $this->loader->ready();
    }

    /**
     * @param string $name
     * @param string $status = 'Sync Complete' ( Sync Pending, Sync Failed))
     *
     * @return \DateTime
     */
    public function getLastSync($name = 'help-desk', $status = 'Sync Complete')
    {
        return $this->sync->getLastSyncTime($name, $status);
    }

    /**
     * Checks if the syncing is complete.
     */
    public function isComplete()
    {
        return $this->loader->getStatus();
    }
}
