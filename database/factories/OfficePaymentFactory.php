<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\OfficePayment;
use Faker\Generator as Faker;

$factory->define(OfficePayment::class, function (Faker $faker) {
    return [
        'office_id' => factory(\App\Office::class),
        'paid'      => $faker->numberBetween(20, 30),
        'assigned'  => $faker->numberBetween(0, 25),
    ];
});

$factory->state(OfficePayment::class, 'completed', [
    'paid'      => 1,
    'assigned'  => 1,
]);

$factory->state(OfficePayment::class, 'incomplete', [
    'paid'      => 10,
    'assigned'  => 0,
]);
