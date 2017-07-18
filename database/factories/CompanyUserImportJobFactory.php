<?php

$factory->define(WA\DataStore\Company\CompanyUserImportJob::class, function (\Faker\Generator $faker) {

    return [
        'jobType'           => $faker->sentence,
        'companyId'         => $faker->numberBetween(1, 20),
        'filepath'          => 'clients/' . $faker->sentence,
        'filename'          => 'employee.csv',
        'totalUsers'        => 0,
        'createdUsers'      => 0,
        'creatableUsers'    => 0,
        'updatedUsers'      => 0,
        'updatableUsers'    => 0,
        'failedUsers'       => 0,
        'fields'            => 'a:22:{i:0;s:5:"email";i:1;s:14:"alternateEmail";i:2;s:8:"password";i:3;s:8:"username";i:4;s:17:"confirmation_code";i:5;s:9:"confirmed";i:6;s:9:"firstName";i:7;s:8:"lastName";i:8;s:18:"alternateFirstName";i:9;s:15:"supervisorEmail";i:10;s:21:"companyUserIdentifier";i:11;s:12:"isSupervisor";i:12;s:11:"isValidator";i:13;s:8:"isActive";i:14;s:11:"defaultLang";i:15;s:5:"level";i:16;s:6:"notify";i:17;s:9:"companyId";i:18;s:12:"supervisorId";i:19;s:10:"externalId";i:20;s:10:"approverId";i:21;s:17:"defaultLocationId";}',
        'sampleUser'        => 'O:8:"stdClass":22:{s:5:"email";s:28:"douglas.rolfson@example.org1";s:14:"alternateEmail";s:24:"pagac.ashlee@example.org";s:8:"password";s:4:"user";s:8:"username";s:15:"douglas.rolfson";s:17:"confirmation_code";s:32:"b95c05f09018e7d91c5a67c8d66b68f4";s:9:"confirmed";s:1:"1";s:9:"firstName";s:7:"Britney";s:8:"lastName";s:8:"Prosacco";s:18:"alternateFirstName";s:7:"Larissa";s:15:"supervisorEmail";s:18:"leon62@example.org";s:21:"companyUserIdentifier";s:1:"2";s:12:"isSupervisor";s:1:"0";s:11:"isValidator";s:1:"0";s:8:"isActive";s:1:"1";s:11:"defaultLang";s:2:"en";s:5:"level";s:1:"0";s:6:"notify";s:1:"0";s:9:"companyId";s:1:"3";s:12:"supervisorId";s:1:"3";s:10:"externalId";s:2:"\N";s:10:"approverId";s:1:"1";s:17:"defaultLocationId";s:2:"52";}',
        'mappings'          => serialize(array()),
        'status'            => 0,
        'created_by_id'     => $faker->numberBetween(1, 20),
        'updated_by_id'     => $faker->numberBetween(1, 20),
        'created_at'        => $faker->dateTime,
        'updated_at'        => $faker->dateTime,
    ];
});
