<?php

use Faker\Generator as Faker;
use Freshbitsweb\Laratables\Tests\Stubs\Models\Country;

$factory->define(Country::class, function (Faker $faker) {
    return [
        'code' => $faker->countryCode,
        'name' => $faker->country,
    ];
});
