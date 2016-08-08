<?php


namespace WA\Repositories;

use Illuminate\Events\Dispatcher;
use WA\DataStore\Device\Device;
use WA\Services\Validation\ValidableInterface as Validator;

class DeviceRepository extends BaseRepository implements DeviceRepositoryInterface
{
    private $events;

    public function __construct(Device $dataStore, Validator $validator = null, Dispatcher $events)
    {
        parent::__construct($dataStore, $validator);

        $this->events = $events;
    }
}
