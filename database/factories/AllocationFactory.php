<?php

$factory->define(\WA\DataStore\Allocation\Allocation::class, function (\Faker\Generator $faker) {

    $carriers = ["ATT", "Verizon", "T-Mobile", "Sprint"];
    $currencies = ['USD'];
    $handsets = ['Apple iPhone 6 16GB Space Gray', 'Samsung Galaxy S4 - Black', 'Google Nexus 6 Xt1103'];
    $bill_months = ['2016-05-01', '2016-06-01', '2016-07-01', '2016-08-01'];


    return [

        'billMonth' => $bill_months[array_rand($bill_months)],
        'mobileNumber' => $faker->e164PhoneNumber,

        'carrier' => $carriers[array_rand($carriers)],
        'currency' => $currencies[array_rand($currencies)],
        'handsetModel' => $handsets[array_rand($handsets)],


        'totalAllocatedCharge' => $faker->biasedNumberBetween(20, 150),
        'preAllocatedAmountDue' => $faker->biasedNumberBetween(20, 150),
        'otherAdjustments' => $faker->biasedNumberBetween(20, 12),
        'preAdjustedAccessCharge' => $faker->biasedNumberBetween(20, 100),

        // Service Plan Charges
        'adjustedAccessCost' => $faker->biasedNumberBetween(20, 14),
        'bBCost' => $faker->biasedNumberBetween(20, 30),
        'pDACost' => $faker->biasedNumberBetween(20, 30),
        'iPhoneCost' => $faker->biasedNumberBetween(20, 10),
        'featuresCost' => $faker->biasedNumberBetween(20, 14),
        'dataCardCost' => $faker->biasedNumberBetween(20, 14),
        'lDCanadaCost' => $faker->biasedNumberBetween(20, 15),
        'uSAddOnPlanCost' => $faker->biasedNumberBetween(20, 13),
        'uSLDAddOnPlanCost' => $faker->biasedNumberBetween(20, 12),
        'uSDataRoamingCost' => $faker->biasedNumberBetween(20, 13),
        'nightAndWeekendAddOnCost' => $faker->biasedNumberBetween(20, 11),
        'minuteAddOnCost' => $faker->biasedNumberBetween(20, 14),

        'servicePlanCharges' => $faker->biasedNumberBetween(20, 12),

        //Usage Charges
        'directConnectCost' => $faker->biasedNumberBetween(20, 10),
        'textMessagingCost' => $faker->biasedNumberBetween(20, 12),
        'dataCost' => $faker->biasedNumberBetween(20, 10),
        'intlRoamingCost' => $faker->biasedNumberBetween(20, 15),
        'intlLongDistanceCost' => $faker->biasedNumberBetween(20, 12),
        'directoryAssistanceCost' => $faker->biasedNumberBetween(20, 11),
        'callForwardingCost' => $faker->biasedNumberBetween(20, 11),
        'airtimeCost' => $faker->biasedNumberBetween(20, 12),

        // summation of usage charges
        'usageCharges' => $faker->biasedNumberBetween(20, 10),

        //Other Charges
        'equipmentCost' => $faker->biasedNumberBetween(20, 24),
        'otherDiscountChargesCost' => $faker->biasedNumberBetween(20, 11),
        'taxes' => $faker->biasedNumberBetween(20, 10),
        'thirdPartyCost' => $faker->biasedNumberBetween(20, 13),

        //summation of other charges
        'otherCharges' => $faker->biasedNumberBetween(20, 10),

        //Fees
        'waFees' => $faker->biasedNumberBetween(12, 1),
        'lineFees' => $faker->biasedNumberBetween(23, 1),
        'mobilityFees' => $faker->biasedNumberBetween(1, 1),

        //summation of fees
        'fees' => $faker->biasedNumberBetween(5, 15),

        //Last Upgrade
        'last_upgrade' => "N/A",

        'userId' => function () {
            return factory(\WA\DataStore\User\User::class)->create()->id;
        },

        'companyId' => function () {
            return factory(WA\DataStore\Company\Company::class)->create()->id;
        },

    ];
});