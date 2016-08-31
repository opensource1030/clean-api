<?php


$factory->define(WA\DataStore\Order\Order::class, function ($faker) {

	$users = factory(\WA\DataStore\User\User::class)->create();
	$package = factory(\WA\DataStore\Package\Package::class)->create();

    return [
        'status'=> $faker->sentence,
        'userId'=> $users->id,
        'packageId'=> $package->id,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
    ];
});