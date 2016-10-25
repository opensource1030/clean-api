<?php

/*
|--------------------------------------------------------------------------
| Users
|--------------------------------------------------------------------------
|
*/
$factory->define(WA\DataStore\User\User::class, function (Faker\Generator $faker) {



    return [
        'identification' => uniqid('WA-'),
        'uuid' => $faker->uuid,
        'email' => $email = $faker->safeEmail,
        'supervisorEmail' => $faker->safeEmail,
        'password' => bcrypt('user'),
        'confirmation_code' => md5(uniqid(mt_rand(), true)),
        'confirmed' => 1,
        'firstName' => $faker->firstName,
        'lastName' => $faker->lastName,
        'username' => explode('@', $email)[0],
        'defaultLang' => 'en',
        'notify' => 0,

        'supervisorId' => $faker->numberBetween(0, 6),
        'approverId' => $faker->numberBetween(0, 4),

        'defaultLocationId' => function () {
            return factory(\WA\DataStore\Location\Location::class)->create()->id;
        },

        'companyId' => 1
    ];
});