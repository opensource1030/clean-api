<?php


namespace WA\Services\Queues;

use Exception;
use Illuminate\Queue\Jobs\Job;
use Log;
use WA\DataStore\Dump;
use WA\Exceptions\Funnel\InvalidFunnelQueueException;
use WA\Managers\FunnelManagerInterface;
use WA\Repositories\DumpRepositoryInterface;

/**
 * Class FunnelQueue.
 */
class FunnelQueue extends BaseService
{
    /**
     * @var DumpRepositoryInterface
     */
    protected $dumps;

    /**
     * @var \WA\Managers\FunnelManagerInterface
     */
    protected $manager;

    /**
     * @param DumpRepositoryInterface $dumps
     * @param FunnelManagerInterface  $manager
     */
    public function __construct(
        DumpRepositoryInterface $dumps,
        FunnelManagerInterface $manager
    ) {
        $this->dumps = $dumps;
        $this->manager = $manager;
    }

    /**
     * Push the job into the queue for further processing.
     *
     * @param $job
     * @param $data
     *
     * @return mixed|void
     *
     * @throws \WA\Exceptions\Funnel\InvalidFunnelQueueException, \Exception
     */
    public function fire(Job $job, $data)
    {

        /* @var \WA\DataStore\Dump $dump */
        $dump = $this->dumps->find($data['dump']);

        $job->delete();

        if (!$dump instanceof Dump) {
            throw new InvalidFunnelQueueException('Invalid dump instance injected into queue for funneling');
        }

        if ($dump->jobstatus->name != 'Funnel queued') {
            throw new InvalidFunnelQueueException("Dump $dump->id is not currently awaiting funneling. ");
        }

        Log::debug("[QUEUE] Received a dump ID: $dump->id job through the queue with data.");

        try {
            $this->setLimits();

            Log::debug("[QUEUE] Funnel job started for dump $dump->id");
            $time_start = microtime(true);
            $this->manager->process($dump);
            $time_end = microtime(true);
            $execution_time = ($time_end - $time_start);
            Log::debug("[QUEUE] Funnel job completed for dump $dump->id in $execution_time seconds.");
        } catch (Exception $e) {
            \Event::fire(
                'funnel.default',
                [
                    [
                        'dump' => $dump,
                        'exception' => $e,
                        'message' => 'Funnel failed in the queue',
                    ],
                ]
            );

            Log::error('[QUEUE] Funnel queue job failed: '.$e->getMessage());
        }
    }
}
