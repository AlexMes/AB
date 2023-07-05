<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\CRM\Tenant;
use Faker\Generator as Faker;

$factory->define(Tenant::class, function (Faker $faker) {
    return [
        'name'          => $faker->name,
        'key'           => \Illuminate\Support\Str::random(),
        'url'           => $faker->url,
        'client_id'     => $faker->numberBetween(),
        'client_secret' => \Illuminate\Support\Str::random(),
    ];
});
