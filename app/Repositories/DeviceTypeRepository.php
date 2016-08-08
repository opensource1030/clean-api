<?php


namespace WA\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Events\Dispatcher;
use WA\DataStore\DeviceType;

/**
 * Class DeviceTypeRepository.
 */
class DeviceTypeRepository extends BaseRepository implements DeviceTypeRepositoryInterface
{
    protected $jobStatus;
    private $events;

    /**
     * @param DeviceType                   $dataStore
     * @param Dispatcher                   $events
     * @param JobStatusRepositoryInterface $jobStatus
     */
    public function __construct(
        DeviceType $dataStore,
        Dispatcher $events,
        JobStatusRepositoryInterface $jobStatus
    ) {
        parent::__construct($dataStore);

        $this->jobStatus = $jobStatus;

        $this->events = $events;
    }

    /**
     * Get the unique fields (as default).
     *
     * @param field
     *
     * @return Collection| \WA\DataStore\DeviceType
     */
    public function getUniqueColumn($field)
    {
        $getColumn =
            $this->dataStore
                ->groupBy($field)->get([$field]);

        return $getColumn;
    }

    /**
     * Get the count of suspended/pending devices.
     */
    public function getPendingCount()
    {
        $pendingStatus = $this->jobStatus->getIdByName('Suspended');

        $count =
            $this->dataStore
                ->where('statusId', $pendingStatus)
                ->count();

        return (int) $count;
    }
}
