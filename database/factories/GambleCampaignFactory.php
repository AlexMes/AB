<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Gamble\Campaign;
use Faker\Generator as Faker;

$factory->define(Campaign::class, function (Faker $faker) {
    return [
        'campaign_id'   => $faker->uuid,
        'name'          => 'Campaign - ' . $faker->words(2, true),
        'account_id'    => factory(\App\Gamble\Account::class),
    ];
});
