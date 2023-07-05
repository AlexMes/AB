<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Gamble\Insight;
use Faker\Generator as Faker;

$factory->define(Insight::class, function (Faker $faker) {
    return [
        'date'          => $faker->date(),
        'account_id'    => factory(\App\Gamble\Account::class),
        'campaign_id'   => factory(\App\Gamble\Campaign::class),
        'impressions'   => $faker->numberBetween(5, 1000),
        'installs'      => $faker->numberBetween(3, 100),
        'spend'         => $faker->randomFloat(2, 3, 100),
        'registrations' => $faker->numberBetween(1, 30),
        'google_app_id' => factory(\App\Gamble\GoogleApp::class),
        'sales'         => $faker->numberBetween(1, 15),
        'deposit_sum'   => $faker->numberBetween(100, 400),
        'deposit_cnt'   => $faker->numberBetween(1, 10),
    ];
});

$factory->state(Insight::class, 'pour-manual', ['pour_type' => 'manual']);
$factory->state(Insight::class, 'pour-auto', ['pour_type' => 'auto']);
