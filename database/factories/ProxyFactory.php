<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Proxy;
use Faker\Generator as Faker;

$factory->define(Proxy::class, function (Faker $faker) {
    return [
        'name'       => $faker->word(),
        'login'      => $faker->email(),
        'password'   => $faker->password(),
        'protocol'   => 'socks5',
        'host'       => $faker->ipv4(),
        'port'       => rand(1000, 50000),
        'geo'        => $faker->countryCode(),
        'branch_id'  => factory(\App\Branch::class),
    ];
});
