<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Modules\Identity\Entities\User;
use Illuminate\Support\Facades\Hash;

$factory->define(User::class, function (Faker $faker) {
  return [
    'email' => $faker->safeEmail,
    'password' => Hash::make('secret'),
    'profile' => [
      'first_name' => $faker->firstName,
      'last_name' => $faker->lastName,
    ],
    'created_at' => $faker->dateTimeBetween($startDate = '-5 years', $endDate = 'now'),
    'updated_at' => $faker->dateTimeBetween($startDate = '-5 years', $endDate = 'now'),
  ];
});


$factory->state(User::class, 'must_verify_email', function(Faker $faker){
  return [
    "profile" => [
      'first_name' => $faker->firstName,
      'last_name' => $faker->lastName,
      'must_verify_email' => true,
      'email_verified_at' => null,
    ],
  ];
});

$factory->state(User::class, 'must_verify_phone', function (Faker $faker) {
  return [
    "profile" => [
      'first_name' => $faker->firstName,
      'last_name' => $faker->lastName,
      'must_verify_phone_number' => true,
      'phone_number_verified_at' => null,
    ],
  ];
});

