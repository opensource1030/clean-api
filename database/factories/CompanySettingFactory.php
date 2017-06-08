<?php


$factory->define(WA\DataStore\Company\CompanySetting::class, function ($faker) {
    $available = ["enable", "disable"];
    $name = ['Bring Your Own Device is allowed', 'Pay for Your Device is allowed'];

    return [
        'value' => $available[array_rand($available)],
        'name' => $name[array_rand($name)],
        'description' => $faker->sentence,
        'companyId' => function () {
            return factory(WA\DataStore\Company\Company::class)->create()->id;
        },
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
    ];
});
