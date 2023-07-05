<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\UnityOrganization;
use Faker\Generator as Faker;

$factory->define(UnityOrganization::class, function (Faker $faker) {
    return [
        'name'                 => $faker->word,
        'organization_core_id' => "{$faker->uuid}",
        'organization_id'      => "{$faker->uuid}",
        'api_key'              => Str::random(),
        'key_id'               => Str::random(),
        'secret_key'           => Str::random(),
    ];
});
