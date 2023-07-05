<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(\App\Server::class, function (Faker $faker) {
    return [
        'forge_id'   => 1,
        'name'       => collect($faker->words(2))->join(' '),
        'provider'   => $faker->word,
        'ip_address' => $faker->ipv4,
        'region'     => $faker->state,
    ];
});

$factory->state(\App\Server::class, 'ready', function (Faker $faker) {
    return [
        'is_ready' => true,
    ];
});
