<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

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

$factory->define(User::class, function (Faker $faker) {
    return [
        'name'              => $faker->name,
        'email'             => $faker->unique()->safeEmail,
        'role'              => User::ADMIN,
        'email_verified_at' => now(),
        'password'          => Str::random(6),
        'remember_token'    => Str::random(10),
    ];
});

$factory->state(User::class, 'admin', fn (Faker $faker) => ['role' => User::ADMIN]);
$factory->state(User::class, 'buyer', fn (Faker $faker) => ['role' => User::BUYER]);
$factory->state(User::class, 'customer', fn (Faker $faker) => ['role' => User::CUSTOMER]);
$factory->state(User::class, 'farmer', fn (Faker $faker) => ['role' => User::FARMER]);
$factory->state(User::class, 'financier', fn (Faker $faker) => ['role' => User::FINANCIER]);
$factory->state(User::class, 'designer', fn (Faker $faker) => ['role' => User::DESIGNER]);
$factory->state(User::class, 'teamlead', fn (Faker $faker) => ['role' => User::TEAMLEAD]);
$factory->state(User::class, 'verifier', fn (Faker $faker) => ['role' => User::VERIFIER]);
$factory->state(User::class, 'gambler', fn (Faker $faker) => ['role' => User::GAMBLER]);
$factory->state(User::class, 'gamble-admin', fn (Faker $faker) => ['role' => User::GAMBLE_ADMIN]);
$factory->state(User::class, 'head', fn (Faker $faker) => ['role' => User::HEAD]);
$factory->state(User::class, 'support', fn (Faker $faker) => ['role' => User::SUPPORT]);
$factory->state(User::class, 'developer', fn (Faker $faker) => ['role' => User::DEVELOPER]);
