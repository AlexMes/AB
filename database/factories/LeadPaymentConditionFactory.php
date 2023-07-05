<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\LeadPaymentCondition;
use Faker\Generator as Faker;

$factory->define(LeadPaymentCondition::class, function (Faker $faker) {
    return [
        'office_id' => factory(\App\Office::class),
        'offer_id'  => factory(\App\Offer::class),
        'model'     => 'cpa',
        'cost'      => $faker->numberBetween(1, 10) * 100,
    ];
});
