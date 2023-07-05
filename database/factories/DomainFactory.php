<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Domain;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(Domain::class, function (Faker $faker) {
    return [
        'effectiveDate'     => null,
        'url'               => $faker->url,
        'status'            => Domain::IN_WORK,
        'linkType'          => Domain::PRELANDING,
        'down_since'        => null,
        'user_id'           => null
    ];
});

$factory->state(Domain::class, 'with_buyer', function () {
    return [
        'user_id' => function () {
            return factory(\App\User::class)->create()->id;
        }
    ];
});

$factory->state(Domain::class, 'scheduled', function ($domain, Faker $faker) {
    return [
        'effectiveDate'   => $faker->date('Y-m-d'),
        'status'          => Domain::SCHEDULED
    ];
});

$factory->state(Domain::class, 'ready', function (Faker $faker) {
    return [
        'status' => Domain::READY
    ];
});

$factory->state(Domain::class, Domain::LANDING, [
    'linkType' => Domain::LANDING
]);
