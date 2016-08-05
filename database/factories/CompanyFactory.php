<?php

$factory->define(WA\DataStore\Company\Company::class, function (\Faker\Generator $faker) {
    return [
        'name' => $company_name = $faker->company,
        'label' => str_replace(" ", "_", strtolower($company_name)),
        'active' => 1,
        'assetPath' => '/var/www/clean/storage/clients/clients/acme',
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
    ];
});