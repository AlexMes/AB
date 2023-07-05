<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\VK\Models\Form;
use Faker\Generator as Faker;

$factory->define(Form::class, function (Faker $faker) {
    return [
        'name'        => $this->faker->unique()->words(2, true),
        'vk_id'       => $this->faker->unique()->randomNumber(9),
        'vk_group_id' => fn () => factory(\App\VK\Models\Group::class)->create()->vk_id,
    ];
});
