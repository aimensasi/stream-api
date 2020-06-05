<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Illuminate\Support\Str;
use Faker\Generator as Faker;
use Modules\Identity\Entities\User;
use Modules\Identity\Entities\Service;
use Illuminate\Support\Facades\Hash;

$factory->define(Service::class, function (Faker $faker) {
  return [
    'access_token' => Hash::make((string) Str::uuid()),
    'refresh_token' => Hash::make((string) Str::uuid()),
    'expires_in' => now()->addHour()->diffInSeconds(),
    'provider' => 'Google',
    'provider_id' => $faker->numberBetween(33333, 99999),
    'user_id' => function(){
      return factory(User::class)->create()->id;
    },
  ];
});