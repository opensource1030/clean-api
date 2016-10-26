<?php

namespace WA\DataStore\Device;

use League\Fractal\Resource\Collection as ResourceCollection;
use League\Fractal\TransformerAbstract;
use WA\DataStore\Asset\AssetTransformer;
use WA\DataStore\Carrier\CarrierTransformer;
use WA\DataStore\DeviceType\DeviceTypeTransformer;
use WA\DataStore\Company\CompanyTransformer;
use WA\DataStore\Modification\ModificationTransformer;
use WA\DataStore\Image\ImageTransformer;
use WA\DataStore\Price\PriceTransformer;
use WA\Helpers\Traits\Criteria;

/**
 * Class DeviceTransformer.
 */
class DeviceTransformer extends TransformerAbstract
{
    use Criteria;

    protected $availableIncludes = [
        'assets', 'carriers', 'companies', 'modifications', 'images', 'prices',
    ];

    protected $defaultIncludes = [
        'devicetypes',
    ];

    /**
     * @param Device $device
     *
     * @return array
     */
    public function transform(Device $device)
    {
        return [
            'id' => (int) $device->id,
            'identification' => $device->identification,
            'name' => $device->name,
            'properties' => $device->properties,
            'externalId' => $device->externalId,
            'statusId' => $device->statusId,
            'syncId' => $device->syncId,
            'created_at' => $device->created_at,
            'updated_at' => $device->updated_at,
        ];
    }

    /**
     * @param Device $device
     *
     * @return ResourceCollection
     */
    public function includeAssets(Device $device)
    {
        $assets = $this->applyCriteria($device->assets(), $this->criteria);

        return new ResourceCollection($assets->get(), new AssetTransformer(), 'assets');
    }

    /**
     * @param Device $device
     *
     * @return ResourceCollection
     */
    public function includeCarriers(Device $device)
    {
        $carriers = $this->applyCriteria($device->carriers(), $this->criteria);

        return new ResourceCollection($carriers->get(), new CarrierTransformer(), 'carriers');
    }

    /**
     * @param Device $device
     *
     * @return ResourceCollection
     */
    public function includeCompanies(Device $device)
    {
        $companies = $this->applyCriteria($device->companies(), $this->criteria);

        return new ResourceCollection($companies->get(), new CompanyTransformer(), 'companies');
    }

    /**
     * @param Device $device
     *
     * @return ResourceCollection
     */
    public function includeModifications(Device $device)
    {
        $modifications = $this->applyCriteria($device->modifications(), $this->criteria);

        return new ResourceCollection($modifications->get(), new ModificationTransformer(), 'modifications');
    }

    /**
     * @param Device $device
     *
     * @return ResourceCollection
     */
    public function includeImages(Device $device)
    {
        $images = $this->applyCriteria($device->images(), $this->criteria);

        return new ResourceCollection($images->get(), new ImageTransformer(), 'images');
    }

    /**
     * @param Device $device
     *
     * @return ResourceCollection
     */
    public function includePrices(Device $device)
    {
        $prices = $this->applyCriteria($device->prices(), $this->criteria);

        return new ResourceCollection($prices->get(), new PriceTransformer(), 'prices');
    }

    /**
     * @param Device $device
     *
     * @return ResourceCollection
     */
    public function includeDevicetypes(Device $device)
    {
        $devicetypes = $this->applyCriteria($device->devicetypes(), $this->criteria);

        return new ResourceCollection($devicetypes->get(), new DeviceTypeTransformer(), 'devicetypes');
    }
}
