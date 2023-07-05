<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\ManualAccount;
use Faker\Generator as Faker;

$factory->define(ManualAccount::class, function (Faker $faker) {
    return [
        'account_id'    => "act_{$faker->uuid}",
        'name'          => $faker->word,
        'user_id'       => factory(\App\User::class),
    ];
});
