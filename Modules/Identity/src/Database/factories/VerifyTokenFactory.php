<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Modules\Identity\Entities\User;
use Modules\Identity\Entities\VerifyToken;

$factory->define(VerifyToken::class, function (Faker $faker) {
  return [
    'user_id' => function(){
      return factory(User::class)->create()->id;
    },
    'token' => VerifyToken::code(),
    'used_for' => $faker->randomElement([VerifyToken::EMAIL, VerifyToken::PASSWORD]),
    'expire_in' => now()->addHours(24),
  ];
});


$factory->state(VerifyToken::class, 'email', [
  'used_for' => VerifyToken::EMAIL,
]);

$factory->state(VerifyToken::class, 'phone_number', [
  'used_for' => VerifyToken::PHONE_NUMBER,
]);

$factory->state(VerifyToken::class, 'password', [
  'used_for' => VerifyToken::PASSWORD,
]);

$factory->state(VerifyToken::class, 'expired', [
  'expire_in' => now()->subDays(2),
]);

$factory->state(VerifyToken::class, 'revoked', [
  'revoked' => true,
]);

$factory->state(VerifyToken::class, 'used', [
  'used_at' => now()->subDays(10),
]);