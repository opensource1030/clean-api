<?php

$factory->define(WA\DataStore\Address\Address::class, function ($faker) {

    return [
        'address' => $faker->sentence,
        'city' => $faker->sentence,
        'state' => $faker->sentence,
        'country' => $faker->sentence,
        'postalCode' => $faker->numberBetween(11111, 99999),
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
    ];
});