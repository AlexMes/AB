<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Firewall;
use Faker\Generator as Faker;

$factory->define(Firewall::class, function (Faker $faker) {
    return [
        'user_id'    => factory(\App\User::class),
        'ip'         => $faker->ipv4,
        'is_allowed' => false,
        'is_blocked' => false,
    ];
});
