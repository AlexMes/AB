<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Gamble\GoogleApp;
use Faker\Generator as Faker;

$factory->define(GoogleApp::class, function (Faker $faker) {
    return [
        'name'      => $faker->firstName,
        'market_id' => $faker->domainName,
        'url'       => $faker->url
    ];
});

$factory->state(GoogleApp::class, 'enabled', ['enabled' => true]);
