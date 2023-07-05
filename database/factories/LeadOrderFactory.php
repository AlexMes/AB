<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(\App\LeadsOrder::class, function (Faker $faker) {
    return [
        'date'      => now(),
        'office_id' => factory(\App\Office::class),
    ];
});
