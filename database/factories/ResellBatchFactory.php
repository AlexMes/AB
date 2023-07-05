<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\ResellBatch;
use Faker\Generator as Faker;

$factory->define(ResellBatch::class, function (Faker $faker) {
    return [
        'name'          => $faker->words(3, true),
        'registered_at' => $faker->dateTime,
        'filters'       => json_encode([]),
        'status'        => ResellBatch::PENDING,
        'assign_until'  => $faker->dateTime,
    ];
});

$factory->state(ResellBatch::class, 'pending', fn (Faker $faker) => ['status' => ResellBatch::PENDING]);
$factory->state(ResellBatch::class, 'in-process', fn (Faker $faker) => ['status' => ResellBatch::IN_PROCESS]);
$factory->state(ResellBatch::class, 'finished', fn (Faker $faker) => ['status' => ResellBatch::FINISHED]);
