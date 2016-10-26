<?php


$factory->define(WA\DataStore\Content\Content::class, function ($faker) {
    return [
        'content' => $faker->paragraph,
        'active' => 1,
        'owner_id' => $faker->numberBetween(0, 9),
        'owner_type' => 'company',
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
    ];
});
