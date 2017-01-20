<?php

$factory->define(WA\DataStore\Category\Preset::class, function (\Faker\Generator $faker) {
	$company = factory(\WA\DataStore\Company\Company::class)->create();
	
    return [
        'name' => $faker->sentence,
        'companyId' => $company->id,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
    ];
});
