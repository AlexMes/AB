<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\User;
use App\VK\Models\Profile;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Profile::class, function (Faker $faker) {
    return [
        'name'    => $this->faker->unique()->words(2, true),
        'vk_id'   => $this->faker->unique()->randomNumber(9),
        'token'   => Str::random(64),
        'user_id' => factory(User::class),
    ];
});
