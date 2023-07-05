<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Result;
use Faker\Generator as Faker;

$factory->define(Result::class, function (Faker $faker) {
    return [
        'date'      => now()->toDateString(),
        'offer_id'  => function () {
            return factory(\App\Offer::class)->create()->id;
        },
        'office_id'  => function () {
            return factory(\App\Office::class)->create()->id;
        },
        'leads_count'        => rand(0, 50),
        'no_answer_count'    => rand(0, 50),
        'reject_count'       => rand(0, 50),
        'wrong_answer_count' => rand(0, 50),
        'demo_count'         => rand(0, 50),
        'ftd_count'          => rand(0, 50),
    ];
});
