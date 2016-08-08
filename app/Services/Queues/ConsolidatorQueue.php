<?php


namespace WA\Services\Queues;

use Exception;
use Illuminate\Queue\Jobs\Job;
use Log;
use WA\DataStore\CarrierDump;
use WA\DataStore\Dump;
use WA\Exceptions\Consolidator\ConsolidatorQueueException;
use WA\Managers\ConsolidatorManagerInterface;
use WA\Repositories\CarrierDumpRepositoryInterface;
use WA\Repositories\DumpRepositoryInterface;

/**
 * Consolidator queue dispatcher.
 */
class ConsolidatorQueue extends BaseService
{
    /**
     * @var \WA\Repositories\CarrierDumpRepositoryInterface
     */
    protected $dumps;

    /**
     * @var \WA\Managers\ConsolidatorManagerInterface
     */
    protected $manager;

    /**
     * @param DumpRepositoryInterface        $dumps
     * @param CarrierDumpRepositoryInterface $cDumps
     * @param ConsolidatorManagerInterface   $manager
     */
    public function __construct(
        DumpRepositoryInterface $dumps,
        CarrierDumpRepositoryInterface $cDumps,
        ConsolidatorManagerInterface $manager
    ) {
        $this->dumps = $dumps;
        $this->cDumps = $cDumps;
        $this->manager = $manager;
    }

    /**
     * Fire the queue job.
     *
     * @param $job \Illuminate\Queue\Jobs\Job
     * @param $data
     *
     * @return mixed|void
     *
     * @throws \WA\Exceptions\Consolidator\ConsolidationException
     */
    public function fire(Job $job, $data)
    {
        if ($data['scope'] == 'full') {
            $dump = $this->dumps->find($data['dump']);
        } else {
            $dump = $this->cDumps->find($data['dump']);
        }

        $job->delete();

        if (!$dump instanceof CarrierDump && !$dump instanceof Dump) {
            throw new ConsolidatorQueueException('Invalid dump instance specified.');
        }

        if ($dump->jobstatus->name != 'Consolidation queued'
            && $dump->jobstatus->name != 'Data Consolidating'
        ) {
            throw new ConsolidatorQueueException("Dump $dump->id is not currently awaiting consolidation. ");
        }

        try {
            $this->setLimits();
            if ($dump instanceof CarrierDump) {
                Log::debug("[QUEUE] Received a carrier dump ID: $dump->id job through the queue with data.");
                Log::debug('[QUEUE] Consolidator ('.$dump->dataMap->type.") started for dump $dump->id");
                $time_start = microtime(true);
                $this->manager->processCarrierDump($dump);
                $time_end = microtime(true);
                $execution_time = ($time_end - $time_start);
                Log::debug(
                    '[QUEUE] Consolidator ('.$dump->dataMap->type.
                    ") completed for dump $dump->id in $execution_time seconds."
                );
            } else {
                Log::debug("[QUEUE] Received a full-scope dump ID: $dump->id job through the queue with data.");
                $time_start = microtime(true);
                $this->manager->process($dump);
                $time_end = microtime(true);
                $execution_time = ($time_end - $time_start);
                Log::debug(
                    "[QUEUE] Full-scope consolidator completed for dump $dump->id in $execution_time seconds."
                );
            }
        } catch (Exception $e) {
            $dump->setStatusByName('Consolidation suspended');
            if ($dump instanceof Dump) {
                $eDump = $dump;
                $ecDump = null;
            } else {
                $eDump = $dump->dump;
                $ecDump = $dump;
            }
            \Event::fire(
                'consolidator.default',
                [
                    [
                        'dump' => $eDump,
                        'carrierDump' => $ecDump,
                        'exception' => $e,
                        'message' => 'Consolidation failed in the queue',
                    ],
                ]
            );

            Log::error('[QUEUE] Consolidator queue job failed: '.$e->getMessage());
        }
    }
}
