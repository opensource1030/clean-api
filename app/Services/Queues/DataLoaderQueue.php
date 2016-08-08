<?php


namespace WA\Services\Queues;

use Exception;
use Illuminate\Queue\Jobs\Job;
use Log;
use Queue;
use WA\DataStore\CarrierDump;
use WA\Exceptions\DataLoader\DataLoadingQueueException;
use WA\Managers\DataLoaderManagerInterface;
use WA\Repositories\CarrierDumpRepositoryInterface;
use WA\Repositories\DumpRepositoryInterface;

/**
 * Class DataLoaderQueue.
 */
class DataLoaderQueue extends BaseService
{
    /**
     * @var \WA\Repositories\CarrierDumpRepositoryInterface
     */
    protected $dumps;

    /**
     * @var \WA\Managers\DataLoaderManagerInterface
     */
    protected $manager;

    /**
     * @param CarrierDumpRepositoryInterface $cDumps
     * @param DumpRepositoryInterface        $dumps
     * @param DataLoaderManagerInterface     $manager
     */
    public function __construct(
        CarrierDumpRepositoryInterface $cDumps,
        DumpRepositoryInterface $dumps,
        DataLoaderManagerInterface $manager
    ) {
        $this->dumps = $dumps;
        $this->cDumps = $cDumps;
        $this->manager = $manager;
    }

    /**
     * @param Job $job
     * @param $data
     *
     * @return mixed|void
     *
     * @throws \WA\Exceptions\DataLoader\DataLoadingQueueException
     */
    public function fire(Job $job, $data)
    {
        $job->delete();
        if (isset($data['type']) && ($data['type'] == 'full')) {
            $dump = $this->dumps->find($data['dump']);
            $this->fireFullDump($dump);
        } else {
            $dump = $this->cDumps->find($data['dump']);
            $this->fireCarrierDump($dump);
        }
    }

    /**
     * @param $dump
     *
     * @return bool
     */
    public function fireFullDump($dump)
    {
        \Log::debug("[QUEUE] Received a full dump ID: $dump->id job through the queue with data.");

        try {
            $this->setLimits();

            Log::debug("[QUEUE] DataLoader started inventory processing for dump $dump->id");
            $time_start = microtime(true);
            $this->manager->processInventory($dump);
            $time_end = microtime(true);
            $execution_time = ($time_end - $time_start);
            Log::debug(
                "[QUEUE] DataLoader completed inventory processing for dump $dump->id in $execution_time seconds."
            );

            /*
             * Now we queue up all the other dumps
             */
            foreach ($dump->getOrderedCarrierDumps() as $cDump) {
                if ($cDump->dataMap->type == 'inv') {
                    continue;
                }

                try {
                    $cDump->setStatusByName('Native file loading queued');
                    Queue::push('WA\Services\Queues\DataLoaderQueue', ['dump' => $cDump->id]);
                } catch (Exception $e) {
                    $cDump->setStatusByName('Native file loading suspended');
                    Log::error('[QUEUE] Unable to queue native file loading: '.$e->getMessage());
                    if (in_array(\App::environment(), ['dev', 'local', 'homestead'])) {
                        throw $e;
                    }
                }
            }

            return true;
        } catch (Exception $e) {
            $dump->setStatusByName('Data Loading');
            \Event::fire(
                'dataloader.default',
                [
                    [
                        'dump' => $dump,
                        'exception' => $e,
                        'message' => 'Dataloading failed in the queue',
                    ],
                ]
            );
            Log::error('[QUEUE] DataLoader full queue job failed: '.$e->getMessage());
        }

        return true;
    }

    /**
     * @param CarrierDump $dump
     */
    public function fireCarrierDump(CarrierDump $dump)
    {
        if (!$dump instanceof CarrierDump) {
            throw new DataLoadingQueueException('Invalid dump instance specified.');
        }

        if ($dump->jobstatus->name != 'Native file loading queued') {
            throw new DataLoadingQueueException('Dump is not ready for native loading');
        }

        \Log::debug("[QUEUE] Received a carrier dump ID: $dump->id job through the queue with data.");

        try {
            $this->setLimits();

            Log::debug('[QUEUE] DataLoader ('.$dump->dataMap->type.") started for dump $dump->id");
            $time_start = microtime(true);
            $this->manager->processCarrierDump($dump);

            $time_end = microtime(true);
            $execution_time = ($time_end - $time_start);
            Log::debug(
                '[QUEUE] DataLoader ('.$dump->dataMap->type.
                ") completed for dump $dump->id in $execution_time seconds."
            );
        } catch (Exception $e) {
            $dump->setStatusByName('Data Loading');
            \Event::fire(
                'dataloader.default',
                [
                    [
                        'dump' => $dump->dump,
                        'carrierDump' => $dump,
                        'exception' => $e,
                        'message' => 'Dataloading failed in the queue',
                    ],
                ]
            );
            Log::error('[QUEUE] DataLoader ('.$dump->dataMap->type.') queue job failed: '.$e->getMessage());
        }
    }
}
