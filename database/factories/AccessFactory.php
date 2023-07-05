<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Access;
use Faker\Generator as Faker;

$factory->define(Access::class, function (Faker $faker) {
    return [
        'received_at'   => now()->toDateString(),
        'name'          => $faker->word,
        'user_id'       => factory(\App\User::class),
        'supplier_id'   => factory(\App\AccessSupplier::class),
        'type'          => $faker->randomElement(['farm', 'brut', 'own']),
        'facebook_url'  => $faker->url,
        'fbId'          => $faker->bankAccountNumber,
        'login'         => $faker->phoneNumber,
        'password'      => $faker->password,
        'email'         => $faker->email,
        'email_password'=> $faker->password,
        'birthday'      => $faker->date(),

    ];
});
