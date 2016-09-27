<?php


$factory->define(WA\DataStore\Order\Order::class, function ($faker) {

	$users = factory(\WA\DataStore\User\User::class)->create();
	$package = factory(\WA\DataStore\Package\Package::class)->create();
	$device = factory(\WA\DataStore\Device\Device::class)->create();
	$service = factory(\WA\DataStore\Service\Service::class)->create();

    return [
        'status'=> $faker->sentence,
        'userId'=> $users->id,
        'packageId'=> $package->id,
        'deviceId' => $device->id,
        'serviceId' => $service->id,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
    ];
});