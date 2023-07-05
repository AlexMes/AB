<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Facebook\Ad;
use Faker\Generator as Faker;

$factory->define(Ad::class, function (Faker $faker) {
    return [
        'id'            => $faker->uuid,
        'name'          => $faker->word,
        'account_id'    => factory(\App\Facebook\Account::class),
        'campaign_id'   => factory(\App\Facebook\Campaign::class),
        'adset_id'      => factory(\App\Facebook\AdSet::class),
    ];
});
