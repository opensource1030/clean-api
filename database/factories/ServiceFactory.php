<?php


$factory->define(WA\DataStore\Service\Service::class, function ($faker) {
    return [
        'title'=> $faker->sentence,
        'planCode'=> $faker->numberBetween(11111, 99999),
        'cost'=> $faker->numberBetween(1, 99),
        'description'=> $faker->paragraph,
        'domesticMinutes'=> $faker->numberBetween(1, 299),
        'domesticData'=> $faker->numberBetween(1, 299),
        'domesticMessages'=> $faker->numberBetween(1, 299),
        'internationalMinutes'=> $faker->numberBetween(1, 299),
        'internationalData'=> $faker->numberBetween(1, 299),
        'internationalMessages'=> $faker->numberBetween(1, 299),
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
    ];
});