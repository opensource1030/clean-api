<?php


$factory->define(WA\DataStore\Service\Service::class, function ($faker) {
    return [
        'status' => 'Enabled',
        'title' => $faker->sentence,
        'planCode' => $faker->numberBetween(11111, 99999),
        'cost' => $faker->numberBetween(100, 199),
        'description' => $faker->paragraph,
        'carrierId' => 1,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
    ];
});
