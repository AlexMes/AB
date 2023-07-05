<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Facebook\AdStatusLog;
use Faker\Generator as Faker;

$factory->define(AdStatusLog::class, function (Faker $faker) {
    return [
        'ad_id'     => factory(\App\Facebook\Ad::class),
        'message'   => $faker->text(),
    ];
});
