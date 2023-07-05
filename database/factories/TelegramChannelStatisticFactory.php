<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\TelegramChannelStatistic;
use Faker\Generator as Faker;

$factory->define(TelegramChannelStatistic::class, function (Faker $faker) {
    return [
        'date'        => now()->toDateString(),
        'channel_id'  => factory(\App\TelegramChannel::class),
        'cost'        => rand(1, 100),
        'impressions' => rand(1000, 100000)
    ];
});
