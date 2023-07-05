<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\AgeInsights;
use Faker\Generator as Faker;

$factory->define(AgeInsights::class, function (Faker $faker) {
    return [
        'date'        => now()->toDateString(),
        'account_id'  => function () {
            return factory(\App\Facebook\Account::class)->create()->account_id;
        },
        'campaign_id' => factory(\App\Facebook\Campaign::class),
        'offer_id'    => function () {
            return factory(\App\Offer::class)->create()->id;
        },
        'reach'             => rand(0, 10000),
        'impressions'       => rand(0, 10000),
        'leads_cnt'         => rand(0, 10),
        'cpc'               => rand(0, 10),
        'ctr'               => rand(0, 10),
        'cpl'               => rand(0, 10),
        'cpm'               => rand(0, 10),
        'clicks'            => rand(0, 10),
        'spend'             => $faker->randomFloat(2, 0, 100),
        'ad_id'             => factory(\App\Facebook\Ad::class),
        'adset_id'          => factory(\App\Facebook\AdSet::class),
        'age'               => $faker->numberBetween(20, 40).'-'.$faker->numberBetween(40, 70),
    ];
});
