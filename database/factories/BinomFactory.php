<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Binom;
use Faker\Generator as Faker;

$factory->define(Binom::class, function (Faker $faker) {
    return [
        'name'         => $faker->words(3, true),
        'enabled'      => false,
        'url'          => $faker->url,
        'access_token' => $faker->uuid,
    ];
});

$factory->state(Binom::class, 'enabled', fn (Faker $faker) => ['enabled' => true]);
$factory->state(Binom::class, 'disabled', fn (Faker $faker) => ['enabled' => false]);
