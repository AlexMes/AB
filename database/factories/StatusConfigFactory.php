<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\StatusConfig;
use Faker\Generator as Faker;

$factory->define(StatusConfig::class, function (Faker $faker) {
    return [
        'office_id'         => factory(\App\Office::class),
        'assigned_days_ago' => $faker->numberBetween(1, 30),
        'new_status'        => $faker->word,
        'statuses'          => $faker->words(),
        'statuses_type'     => StatusConfig::IN,
        'enabled'           => false,
    ];
});

$factory->state(StatusConfig::class, 'enabled', ['enabled' => true]);
$factory->state(StatusConfig::class, 'disabled', ['enabled' => false]);
$factory->state(StatusConfig::class, 'in', ['statuses_type' => StatusConfig::IN]);
$factory->state(StatusConfig::class, 'out', ['statuses_type' => StatusConfig::OUT]);
