<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\ManualApp;
use Faker\Generator as Faker;

$factory->define(ManualApp::class, function (Faker $faker) {
    return [
        'link' => $faker->url,
    ];
});

$factory->state(ManualApp::class, 'new', fn (Faker $faker) => ['status' => 'new']);
$factory->state(ManualApp::class, 'published', fn (Faker $faker) => ['status' => 'published']);
$factory->state(ManualApp::class, 'banned', fn (Faker $faker) => ['status' => 'banned']);
