<?php


$factory->define(WA\DataStore\Page\Page::class, function ($faker) {
    return [
        'title' => $faker->sentence,
        'section' => $faker->sentence,
        'content' => $faker->paragraph,
        'active' => 1,
        'owner_id' => $faker->numberBetween(0, 9),
        'owner_type' => 'company',
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
    ];
});
