<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Gamble\GoogleAppLink;
use Faker\Generator as Faker;

$factory->define(GoogleAppLink::class, function (Faker $faker) {
    return [
        'url'       => $faker->url,
        'app_id'    => factory(\App\Gamble\GoogleApp::class),
        'user_id'   => factory(\App\Gamble\User::class),
    ];
});

$factory->state(GoogleAppLink::class, 'enabled', ['enabled' => true]);
