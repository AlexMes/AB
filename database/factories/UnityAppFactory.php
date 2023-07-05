<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\UnityApp;
use Faker\Generator as Faker;

$factory->define(UnityApp::class, function (Faker $faker) {
    return [
        'id'              => "$faker->uuid",
        'name'            => $faker->word,
        'store'           => 'google',
        'store_id'        => $faker->domainName,
        'organization_id' => fn () => factory(\App\UnityOrganization::class)->create(),
    ];
});
