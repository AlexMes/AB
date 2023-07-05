<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Site;
use Faker\Generator as Faker;

$factory->define(Site::class, function (Faker $faker) {
    return [
        'forge_id'         => $faker->randomNumber(),
        'server_id'        => fn () => factory(\App\Server::class)->create()->id,
        'name'             => $faker->domainWord,
    ];
});

$factory->state(Site::class, 'has_certificates', function (Faker $faker) {
    return [
        'has_certificates' => true,
    ];
});

$factory->state(Site::class, 'has_not_certificates', function (Faker $faker) {
    return [
        'has_certificates' => false,
    ];
});
