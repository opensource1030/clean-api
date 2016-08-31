<?php


$factory->define(WA\DataStore\Modification\Modification::class, function ($faker) {

    $carriers = ["Capacity", "Style"];

    return [
        'type'=> $carriers[array_rand($carriers)],
        'value'=> $faker->sentence,
        'updated_at' => $faker->dateTime,
        'created_at' => $faker->dateTime
    ];
});