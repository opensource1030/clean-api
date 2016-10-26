<?php
/*
|--------------------------------------------------------------------------
| Devices: Representation of devices we manage
|
| Device Types: types of devices we manage
|--------------------------------------------------------------------------
|
*/

$factory->define(\WA\DataStore\Price\Price::class,
    function (\Faker\Generator $faker) {
        $device = factory(\WA\DataStore\Device\Device::class)->create();
        $capacity = factory(\WA\DataStore\Modification\Modification::class)->create(
            ['modType' => 'capacity']
        );
        $style = factory(\WA\DataStore\Modification\Modification::class)->create(
            ['modType' => 'style']
        );
        $carrier = factory(\WA\DataStore\Carrier\Carrier::class)->create();
        $company = factory(\WA\DataStore\Company\Company::class)->create();

        return [
            'deviceId' => $device->id,
            'capacityId' => $capacity->id,
            'styleId' => $style->id,
            'carrierId' => $carrier->id,
            'companyId' => $company->id,
            'priceRetail' => $faker->numberBetween(300, 599),
            'price1' => $faker->numberBetween(300, 599),
            'price2' => $faker->numberBetween(300, 599),
            'priceOwn' => $faker->numberBetween(300, 599),
        ];
    }
);
