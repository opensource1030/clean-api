<?php


$factory->define(WA\DataStore\Condition\Condition::class, function ($faker) {

    return [
        'profileNameCondition' => $faker->sentence,
        'profileNameValue' => $faker->sentence,
        'profileEmailCondition' => $faker->sentence,
        'profileEmailValue' => $faker->sentence,
        'profilePositionCondition' => $faker->sentence,
        'profilePositionValue' => $faker->sentence,
        'profileLevelCondition' => $faker->sentence,
        'profileLevelValue' => $faker->sentence,
        'profileDivisionCondition' => $faker->sentence,
        'profileDivisionValue' => $faker->sentence,
        'profileCostCenterCondition' => $faker->sentence,
        'profileCostCenterValue' => $faker->sentence,
        'profileBudgetCondition' => $faker->sentence,
        'profileBudgetValue' => $faker->sentence,
        'locationItemsCountryACondition' => $faker->sentence,
        'locationItemsCountryAValue' => $faker->sentence,
        'locationItemsCountryBCondition' => $faker->sentence,
        'locationItemsCountryBValue' => $faker->sentence,
        'locationItemsCityCondition' => $faker->sentence,
        'locationItemsCityValue' => $faker->sentence,
        'locationItemsAdressCondition' => $faker->sentence,
        'locationItemsAdressValue' => $faker->sentence,
    ];
});