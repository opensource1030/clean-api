<?php


$factory->define(WA\DataStore\Package\Package::class, function ($faker) {

	$condition = factory(\WA\DataStore\Condition\Condition::class)->create();
	$device = factory(\WA\DataStore\Device\Device::class)->create();
	$app = factory(\WA\DataStore\App\App::class)->create();
	$service = factory(\WA\DataStore\Service\Service::class)->create();

    return [
        'name'=> $faker->sentence,
        'conditionsId' => $condition->id,
        'devicesId' => $device->id,
        'appsId' => $app->id,
        'servicesId' => $service->id,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
    ];
});