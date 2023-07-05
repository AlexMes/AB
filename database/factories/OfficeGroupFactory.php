<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\OfficeGroup;
use Faker\Generator as Faker;

$factory->define(OfficeGroup::class, function (Faker $faker) {
    return [
        'name'      => $faker->word(),
        'branch_id' => fn () => factory(\App\Branch::class),
    ];
});
