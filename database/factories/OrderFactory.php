<?php


$factory->define(WA\DataStore\Order\Order::class, function ($faker) {
    $status = ['Approval', 'New', 'Delivered', 'Expired'];
    $orderType = ['NewLineOfService', 'UpgradeDevice', 'TransferServiceLiability', 'Accessories'];

    return [
        'status' => $status[array_rand($status)],
        'orderType' => $orderType[array_rand($orderType)], 
        'serviceImei' => rand(100000000000000, 999999999999999), 
        'servicePhoneNo' => rand(600000000, 699999999),
        'serviceSim' => rand(100000000, 999999999), 
        'deviceImei' => rand(100000000000000, 999999999999999), 
        'deviceCarrier' => $faker->sentence, 
        'deviceSim' => rand(100000000, 999999999),
        'userId' => rand(1,20),
        'packageId' => rand(1,20),
        'serviceId' => rand(1,20),
        'addressId' => rand(1,20),
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
    ];
});

