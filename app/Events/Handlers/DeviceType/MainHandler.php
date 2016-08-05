<?php

namespace WA\Events\Handlers\DeviceType;

use Illuminate\Events\Dispatcher;
use WA\DataStore\Carrier\Carrier;
use WA\DataStore\CarrierDevice;
use WA\DataStore\DeviceType;
use WA\Events\Handlers\BaseHandler;
use WA\Helpers\Helper as h;
use WA\Repositories\JobStatusRepositoryInterface;

/**
 * Class MainHandler.
 */
class MainHandler extends BaseHandler
{
    /**
     * @var \WA\Repositories\JobStatusRepositoryInterface
     */
    protected $jobStatus;

    /**
     * @param JobStatusRepositoryInterface $jobStatus
     */
    public function __construct(JobStatusRepositoryInterface $jobStatus = null)
    {
        $this->jobStatus = $jobStatus;
    }

    /**
     * @param $data
     *
     * @return \Illuminate\Database\Eloquent\Model|static
     */
    public function onDeviceTypeMissing($data)
    {
        $deviceType = $this->addDeviceType($data);

        return $deviceType;
    }

    /**
     * @param $line
     *
     * @return \Illuminate\Database\Eloquent\Model|static
     */
    protected function addDeviceType($line)
    {

        $pendingStatus = $this->jobStatus->getIdByName('Pending Review');
        $carrier = $line->{'Carrier'};

        $class = 'pending';

        list($carrierModel, $carrierMake, $deviceOS) = $this->extractDeviceMakeModel($line, $carrier);

        if (stripos($carrierModel, 'ipad') !== false) {
            $class = 'Tablet';
            $deviceOS = 'Apple';
        } elseif (stripos($carrierMake, 'apple') !== false || stripos($carrierModel, 'iphone') !== false) {
            $class = 'Smart Phone';
            $deviceOS = 'Apple';
        } elseif (stripos($carrierMake, 'rim') !== false || stripos($carrierModel, 'blackberry') !== false) {
            $class = 'Smart Phone';
            $deviceOS = 'Blackberry';
        }

        // Some post processing for all carriers
        if (h::startsWith(strtolower($carrierModel), 'motorola')) {
            $carrierModel = substr($carrierModel, stripos($carrierModel, 'motorola'));
        } elseif (stripos($carrierModel, 'apple')) {
            $carrierModel = substr($carrierModel, stripos($carrierModel, 'apple'));
        } elseif (h::startsWith(strtolower($carrierModel), 'samsung')) {
            $carrierModel = substr($carrierModel, stripos($carrierModel, 'samsung'));
        } elseif (h::startsWith(strtolower($carrierModel), 'rim')) {
            $carrierModel = substr($carrierModel, stripos($carrierModel, 'rim'));
        } elseif (h::startsWith(strtolower($carrierModel), 'casio')) {
            $carrierModel = substr($carrierModel, stripos($carrierModel, 'casio'));
        } elseif (h::startsWith(strtolower($carrierModel), 'lg')) {
            $carrierModel = substr($carrierModel, stripos($carrierModel, 'lg'));
        } elseif (h::startsWith(strtolower($carrierModel), 'pantech')) {
            $carrierModel = substr($carrierModel, stripos($carrierModel, 'pantech'));
        }

        $dt = DeviceType::where('make', $carrierMake)->where('model', $carrierModel)->first();

        if ($dt !== null) {
            return $dt;
        }

        $dt = new DeviceType();
        $dt->make = $carrierMake;
        $dt->model = $carrierModel;
        $dt->class = $class;
        $dt->description = 'pending review';
        $dt->statusId = $pendingStatus;
        $dt->save();

        return $dt;
    }

    /**
     * @param $line
     * @param $carrier
     *
     * @return array
     */
    protected function extractDeviceMakeModel($line, $carrier)
    {
        if ($carrier == 'ATT') {
            return $this->getATTMakeModel($line);
        } elseif ($carrier == 'Rogers') {
            return $this->getRogersMakeModel($line);
        } elseif ($carrier == 'Verizon') {
            return $this->getVerizonMakeModel($line);
        } elseif ($carrier == 'BellCanada') {
            return $this->getBellCanadaMakeModel($line);
        } else {
            return $this->getGenericMakeModel($line, $carrier);
        }
    }

    /**
     * @param $line
     *
     * @return array
     */
    protected function getATTMakeModel($line)
    {
        $carrierModel = trim($line->{'Phone or Device Model'});
        $carrierMake = trim($line->{'Phone or Device Make'});
        $deviceOS = trim($line->{'Phone or Device Make'});

        return array($carrierModel, $carrierMake, $deviceOS);
    }

    /**
     * @param $line
     *
     * @return array
     */
    protected function getRogersMakeModel($line)
    {
        $deviceOS = 'unknown';
        $carrierModel = trim($line->{'Phone or Device Model'});
        if (stripos($carrierModel, 'ipad') !== false ||
            stripos($carrierModel, 'iphone') !== false ||
            stripos($carrierModel, 'apple') !== false
        ) {
            $carrierMake = 'Apple';
            $deviceOS = 'Apple';
        } elseif (h::startsWith($carrierModel, 'LG')) {
            $carrierMake = 'LG';
        } elseif (h::startsWith($carrierModel, 'SCH') ||
            h::startsWith(strtolower($carrierModel), 'samsung')
        ) {
            $carrierMake = 'Samsung';
        } elseif (h::startsWith(strtolower($carrierModel), 'casio')) {
            $carrierMake = 'Casio';
        } elseif (h::startsWith(strtolower($carrierModel), 'rim') ||
            stripos($carrierModel, 'blackberry') !== false
        ) {
            $carrierMake = 'RIM';
            $deviceOS = 'Blackberry';
        } elseif (stripos($carrierModel, 'moto') !== false) {
            $carrierMake = 'Motorola';
        } elseif (h::startsWith($carrierModel, 'Pantech')) {
            $carrierMake = 'Pantech';
        } else {
            $carrierMake = $this->findUnknownMake($line, 'Phone or Device Make');
        }

        return array($carrierModel, $carrierMake, $deviceOS);
    }

    /**
     * @param $line
     * @param string $needle
     *
     * @return string
     */
    protected function findUnknownMake($line, $needle = 'Phone or Device Make')
    {
        $carrierMake = 'unknown';
        $makes = array_flatten(DeviceType::select('make')->distinct()->get()->toArray());
        $firstBit = substr($line->{$needle}, 0, strpos($line->{$needle}, ' '));
        $makeRes = $this->customSearch($firstBit, $makes);

        if ($makeRes !== false) {
            $carrierMake = $makes[$makeRes];

            return $carrierMake;
        }

        return $carrierMake;
    }

    /**
     * @param $keyword
     * @param $arrayToSearch
     *
     * @return bool|int|string
     */
    public function customSearch($keyword, $arrayToSearch)
    {
        if ($keyword == '' || $keyword == null) {
            return false;
        }
        foreach ($arrayToSearch as $key => $arrayItem) {
            if (stristr($arrayItem, $keyword)) {
                return $key;
            }
        }

        return false;
    }

    /**
     * @param $line
     *
     * @return array
     */
    protected function getVerizonMakeModel($line)
    {
        $deviceOS = 'unknown';
        if (!isset($line->{'Device Manufacturer'}) && isset($line->{'Device_Manufacturer'})) {
            $line->{'Device Manufacturer'} = $line->{'Device_Manufacturer'};
        }
        $carrierModel = trim($line->{'Phone or Device Model'});
        switch (trim($line->{'Device Manufacturer'})) {
            case '':
                if (stripos($carrierModel, 'ipad') !== false ||
                    stripos($carrierModel, 'iphone') !== false ||
                    stripos($carrierModel, 'apple') !== false
                ) {
                    $carrierMake = 'Apple';
                    $deviceOS = 'Apple';
                } elseif (h::startsWith($carrierModel, 'LG')) {
                    $carrierMake = 'LG';
                } elseif (h::startsWith($carrierModel, 'SCH') ||
                    h::startsWith(strtolower($carrierModel), 'samsung')
                ) {
                    $carrierMake = 'Samsung';
                } elseif (h::startsWith(strtolower($carrierModel), 'casio')) {
                    $carrierMake = 'Casio';
                } elseif (stripos($carrierModel, 'blackberry') !== false) {
                    $carrierMake = 'RIM';
                    $deviceOS = 'Blackberry';
                } elseif (stripos($carrierModel, 'moto') !== false) {
                    $carrierMake = 'Motorola';
                } elseif (h::startsWith($carrierModel, 'Pantech')) {
                    $carrierMake = 'Pantech';
                } else {
                    $carrierMake = $this->findUnknownMake($line, 'Phone or Device Make');
                }
                break;
            case 'NOK':
            case 'NOKIA':
                $carrierMake = 'Nokia';
                break;
            case 'RESEARCH IN MOTION':
            case 'RIM':
                $carrierMake = 'RIM';
                $deviceOS = 'Blackberry';
                break;
            case 'SIERRA WIRELESS':
                $carrierMake = 'Sierra Wireless';
                break;
            case 'UTS':
            case 'UTSTARCOM':
                $carrierMake = 'Pantech';
                break;
            case 'LG':
                $carrierMake = 'LG';
                break;
            case 'SAMSUNG':
            case 'SAM':
                $carrierMake = 'Samsung';
                break;
            case 'GTO':
                $carrierMake = 'Gemalto';
                break;
            case 'MOTOROLA':
            case 'MOT':
                $carrierMake = 'Motorola';
                break;
            case 'G_D':
                $carrierMake = 'G&D';
                break;
            case 'NOVATEL':
            case 'NOV':
                $carrierMake = 'Novatel Wireless';
                break;
            case 'APPLE':
            case 'APL':
                $carrierMake = 'Apple';
                $deviceOS = 'Apple';
                break;
            case 'QUALCOMM':
                $carrierMake = 'Qualcomm';
                break;
            default:
                $carrierMake = $this->findUnknownMake($line);
                \Log::error(
                    '[' . get_class($this) . '] Unknown device manufacturer: ' . trim($line->{'Device Manufacturer'})
                );
                break;
        }

        return array($carrierModel, $carrierMake, $deviceOS);
    }

    /**
     * @param $line
     *
     * @return array
     */
    protected function getBellCanadaMakeModel($line)
    {
        $deviceOS = 'unknown';
        $carrierModel = trim($line->{'Model description'});

        switch ($line->{'Device type'}) {
            case 'PDA':
                $deviceOS = 'Smart Phone';
                $carrierMake = $this->findUnknownMake($line, 'Model description');
                break;
            case 'Blackberry':
                $carrierMake = 'RIM';
                $deviceOS = 'Blackberry';
                break;
            case 'iPhone':
                $carrierMake = 'Apple';
                $deviceOS = 'Apple';
                break;
            default:
                $carrierMake = $this->findUnknownMake($line, 'Model description');
                \Log::error('Unknown device type: ' . trim($line->{'Device type'}));
                break;
        }

        return array($carrierModel, $carrierMake, $deviceOS);
    }

    /**
     * @param $line
     * @param $carrier
     *
     * @return array
     */
    protected function getGenericMakeModel($line, $carrier)
    {
        $carrierModel = 'unknown';
        $carrierMake = 'unknown';
        $deviceOS = 'unknown';

        \Log::error('[' . get_class($this) . "] Unknown carrier $carrier.");

        return array($carrierModel, $carrierMake, $deviceOS);
    }

    /**
     * @param $data
     *
     * @return \Illuminate\Database\Eloquent\Model|static
     */
    public function onCarrierDeviceMissing($data)
    {
        $carrierDevice = $this->addCarrierDevice($data);

        return $carrierDevice;
    }

    /**
     * @param $line
     * @param string $makeColumn
     * @param string $modelColumn
     *
     * @return static
     */
    protected function addCarrierDevice(
        $line,
        $makeColumn = 'Phone or Device Make',
        $modelColumn = 'Phone or Device Model'
    ) {
        $pendingStatus = $this->jobStatus->getIdByName('Pending Review');

        $deviceType = $this->addDeviceType($line);

        if (!isset($deviceType)) {
            \Log::error('[' . get_class($this) . '] Failed to create a new device type.');
        }

        $cd = CarrierDevice::create(
            [
                'carrierId'    => Carrier::where('name', $line->{'Carrier'})->pluck('id'),
                'deviceTypeId' => (isset($deviceType->id) ? $deviceType->id : null),
                'make'         => trim($line->{$makeColumn}),
                'model'        => trim($line->{$modelColumn}),
                'makeModel'    => trim($line->{$makeColumn}) . ' ' .
                    trim($line->{$modelColumn}),
                'WA_alias'     => trim($line->{$modelColumn}),
                'class'        => 'pending',
                'deviceOS'     => null,
                'description'  => 'pending review',
                'statusId'     => $pendingStatus,
            ]
        );

        return $cd;
    }

    /**
     * @param Dispatcher $events
     */
    public function subscribe(Dispatcher $events)
    {
        $events->listen('deviceType.missing', 'WA\Events\Handlers\DeviceType\MainHandler@onDeviceTypeMissing');

        $events->listen('carrierDevice.missing', 'WA\Events\Handlers\DeviceType\MainHandler@onCarrierDeviceMissing');
    }
}
