<?php

use Faker\Generator as Faker;
use Freshbitsweb\Laratables\Tests\Stubs\Models\Country;
use Freshbitsweb\Laratables\Tests\Stubs\Models\User;
use Illuminate\Support\Str;

$factory->define(User::class, function (Faker $faker) {
    return [
        'country_id' => Country::inRandomOrder()->value('id'),
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
        'remember_token' => Str::random(10),
    ];
});
