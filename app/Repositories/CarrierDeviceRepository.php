<?php


namespace WA\Repositories;

use Illuminate\Events\Dispatcher;
use WA\DataStore\Carrier\Carrier;
use WA\DataStore\CarrierDevice;

/**
 * Class CarrierDeviceRepository.
 */
class CarrierDeviceRepository extends BaseRepository implements CarrierDeviceRepositoryInterface
{
    protected $jobStatus;
    private $events;

    /**
     * @param CarrierDevice                $dataStore
     * @param Dispatcher                   $events
     * @param JobStatusRepositoryInterface $jobStatus
     */
    public function __construct(
        CarrierDevice $dataStore,
        Dispatcher $events,
        JobStatusRepositoryInterface $jobStatus
    ) {
        parent::__construct($dataStore);

        $this->jobStatus = $jobStatus;

        $this->events = $events;
        $this->pendingStatus = $this->jobStatus->getIdByName('Pending Review');
    }

    /**
     * @param $id
     *
     * @return bool
     */
    public function getNextPending($id)
    {
        $next = $this->dataStore->where('id', '<>', $id)->where('statusId', $this->pendingStatus)->first();
        if ($next == null) {
            return false;
        }

        return $next;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getPending()
    {
        $pending = $this->dataStore->where('statusId', $this->pendingStatus);

        return $pending;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPendingDevices()
    {
        return $this->getPending()->get();
    }

    /**
     * Get the count of suspended/pending devices.
     *
     * @return int
     */
    public function getPendingCount()
    {
        return (int) $this->getPending()->count();
    }

    public function create(array $attributes = [])
    {
        $defaultCarrierId = Carrier::where('name', 'System')->pluck('id');

        if (!array_key_exists('makeModel', $attributes)) {
            $attributes[ 'makeModel' ] = $attributes[ 'make' ].' '.$attributes[ 'model' ];
        }

        if (!array_key_exists('carrierId', $attributes)) {
            $attributes[ 'carrierId' ] = $defaultCarrierId;
        }

        if (!array_key_exists('WA_alias', $attributes)) {
            $attributes[ 'WA_alias' ] = $attributes[ 'make' ].' '.$attributes[ 'model' ];
        }

        return parent::create($attributes);
    }
}
