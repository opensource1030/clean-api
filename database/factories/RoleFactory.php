<?php

$factory->define(WA\DataStore\Role\Role::class, function ($faker) {
    return [
        'name' => $faker->sentence
    ];
});
