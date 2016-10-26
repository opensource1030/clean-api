<?php


$factory->define(WA\DataStore\Package\Package::class, function ($faker) {
    return [
        'name' => $faker->sentence,
        'addressId' => function () {
            return factory(WA\DataStore\Address\Address::class)->create()->id;
        },
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
    ];
});
