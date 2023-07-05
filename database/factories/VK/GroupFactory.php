<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\VK\Models\Group;
use Faker\Generator as Faker;

$factory->define(Group::class, function (Faker $faker) {
    return [
        'name'       => $this->faker->unique()->words(2, true),
        'vk_id'      => $this->faker->unique()->randomNumber(9),
        'profile_id' => factory(\App\VK\Models\Profile::class),
    ];
});
