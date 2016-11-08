<?php

$factory->define(WA\DataStore\Addon\Addon::class, function ($faker) {
    return [
        'name' => $faker->sentence,
        'cost' => $faker->numberBetween(20, 50),
        'serviceId' => $faker->numberBetween(1, 4),
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
    ];
});
