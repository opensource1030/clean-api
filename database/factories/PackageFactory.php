<?php


$factory->define(WA\DataStore\Package\Package::class, function ($faker) {
    return [
        'name'=> $faker->sentence,
        'information'=> $faker->sentence,
        'approvalCode'=> $faker->sentence,
        'companyId' => 1,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
    ];
});
