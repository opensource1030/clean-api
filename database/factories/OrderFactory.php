<?php


$factory->define(WA\DataStore\Order\Order::class, function ($faker) {
    $status = ['Approval', 'New', 'Delivered', 'Expired'];

    return [
        'status' => $status[array_rand($status)],
        'phoneno' => rand(600000000, 699999999),
        'imei' => rand(100000000000000, 999999999999999),
        'sim' => rand(100000000, 999999999),
        'userId' => rand(1,20),
        'packageId' => rand(1,20),
        'serviceId' => rand(1,20),
        'addressId' => rand(1,20),
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
    ];
});
