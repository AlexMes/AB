<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Order;
use Faker\Generator as Faker;

$factory->define(Order::class, function (Faker $faker) {
    return [
        'links_count'    => $faker->randomNumber(2),
        'linkType'       => Order::TYPE_DOMAINS,
        'useCloudflare'  => false,
        'useConstructor' => false,
    ];
});

$factory->state(Order::class, Order::TYPE_DOMAINS, fn (Faker $faker) => ['linkType' => Order::TYPE_DOMAINS]);
$factory->state(Order::class, Order::TYPE_SUBDOMAINS, fn (Faker $faker) => ['linkType' => Order::TYPE_SUBDOMAINS]);
