<?php


$factory->define(WA\DataStore\Modification\Modification::class, function ($faker) {

    $modifications = ["Capacity", "Style"];

    return [
        'type'=> $modifications[array_rand($modifications)],
        'value'=> $faker->sentence,
        'updated_at' => $faker->dateTime,
        'created_at' => $faker->dateTime
    ];
});