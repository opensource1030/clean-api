<?php

$factory->define(WA\DataStore\Category\Preset::class, function ($faker) {
    return [
        'name' => $faker->sentence,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
    ];
});
