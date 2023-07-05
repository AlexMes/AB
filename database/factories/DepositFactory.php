<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Deposit;
use Faker\Generator as Faker;

$factory->define(Deposit::class, function (Faker $faker) {
    return [
        'lead_return_date' => now(),
        'date'             => now(),
        'phone'            => '7977895875'
    ];
});
