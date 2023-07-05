<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Facebook\Campaign;
use Faker\Generator as Faker;

$factory->define(Campaign::class, function (Faker $faker) {
    return [
        'id'              => $faker->uuid,
        'name'            => $faker->word,
        'account_id'      => function () {
            return factory(\App\Facebook\Account::class)->create()->account_id;
        },
        'daily_budget'      => rand(0, 1000),
        'lifetime_budget'   => rand(0, 1000),
        'buying_type'       => 'AUCTION',
        'status'            => '',
        'effective_status'  => 'ACTIVE',
        'configured_status' => 'ACTIVE',
    ];
});

// spl > 8.5
$factory->state(Campaign::class, 'stopped_by_cpl', function (Faker $faker) {
    return [];
})->afterCreatingState(Campaign::class, 'stopped_by_cpl', function ($campaign) {
    factory(\App\Insights::class, 5)->create([
        'campaign_id'       => $campaign->id,
        'account_id'        => $campaign->account_id,
        'leads_cnt'         => 30,
        'spend'             => 300, // cpl 300 / 30 = 10
    ]);
});

// spend > 5
$factory->state(Campaign::class, 'stopped_by_spend', function (Faker $faker) {
    return [];
})->afterCreatingState(Campaign::class, 'stopped_by_spend', function ($campaign) {
    factory(\App\Insights::class, 5)->create([
        'campaign_id'       => $campaign->id,
        'account_id'        => $campaign->account_id,
        'leads_cnt'         => 0,
        'spend'             => 300,
    ]);
});

$factory->state(Campaign::class, 'unstopped', function (Faker $faker) {
    return [];
})->afterCreatingState(Campaign::class, 'unstopped', function ($campaign) {
    factory(\App\Insights::class, 5)->create([
        'campaign_id'       => $campaign->id,
        'account_id'        => $campaign->account_id,
        'leads_cnt'         => 100,
        'spend'             => 300, // cpl 300 / 100 = 3
    ]);
});
