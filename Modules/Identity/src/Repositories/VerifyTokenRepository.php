<?php

namespace Modules\Identity\Repositories;

use Illuminate\Support\Arr;
use Modules\Identity\Helpers\Common;
use Modules\Identity\Identity;
use Modules\Identity\Entities\VerifyToken;
use Modules\Identity\Exceptions\InvalidTokenException;

class VerifyTokenRepository {

	/**
	 * function name to create a verify token for a given use case
	 * 
	 * @var string
	 **/
	private static $createVerifyTokenFor = 'createVerifyTokenFor';

	/**
	 * function name to verify a token for a given use case
	 * 
	 * @var string
	 **/
	private static $verifyTokenFor = 'verifyTokenFor';


	public static function __callStatic($method, $arguments){
		switch($method){
			case Common::startWith($method, self::$createVerifyTokenFor):
				$attribute = Common::getFunctionAttribute($method, self::$createVerifyTokenFor);

				return self::createVerifyTokenFor($attribute, $arguments);
				break;
			case Common::startWith($method, self::$verifyTokenFor):
				$attribute = Common::getFunctionAttribute($method, self::$verifyTokenFor);
				
				return self::verifyTokenFor($attribute, $arguments);
				break;
			default:
				return false;
				break;
		}
	}


	/**
	 * Create Verify Token for the given user
	 * 
	 * @param array $attributes
	 * @param int $userId
	 * @return VerifyToken
	 **/
	public static function createVerifyToken($attributes, $userId): VerifyToken{
		$verifyToken = Identity::verifyToken();

		$attributes['user_id'] = $userId;

		$verifyToken = $verifyToken::create($attributes);

		return $verifyToken;
	}


	/**
	 * Create Verify Token for the given user
	 * 
	 * @param array $attributes
	 * @param int $userId
	 * @return VerifyToken
	 **/
	public static function createVerifyTokenFor($type, $arguments): VerifyToken{
		$arguments = Arr::flatten($arguments);
		$userId = $arguments[0];
		$verifyToken = Identity::verifyToken();

		self::revokeVerifyTokens($type, $userId);
		$verifyToken = $verifyToken::identity($userId, $type, $verifyToken::CODE);

		return $verifyToken;
	}

	/**
	 * Verify Token For a given use case 
	 * 
	 * @param string $attribute
	 * @param array $arguments
	 * @return VerifyToken
	 **/
	public static function verifyTokenFor($attribute, $arguments): VerifyToken{
		$arguments = Arr::flatten($arguments);
		
		$verifyToken = Identity::verifyToken();

		$token = $verifyToken::where('token', $arguments[0])
			->where('used_for', $attribute)
			->where('expire_in', '>', now())
			->where('used_at', null)
			->where('revoked', false)
			->first();


		if (!$token) {
			throw new InvalidTokenException();
		}

		return $token;
	}


	/**
	 * Revoke all verification tokens for the given user
	 * 
	 * @param int $userId
	 * @return void
	 **/
	public static function revokeVerifyTokensFor($userId): void{
		$verifyToken = Identity::verifyToken();

		$verifyToken::where('user_id', $userId)
			->where('revoked', false)
			->update(['revoked' => true]);
	}

	/**
	 * Revoke specific verification tokens type for the given user
	 * 
	 * @param int $userId
	 * @return void
	 **/
	public static function revokeVerifyTokens($type, $userId): void{
		$verifyToken = Identity::verifyToken();

		$verifyToken::where('user_id', $userId)
			->where('used_for', $type)
			->where('revoked', false)
			->update(['revoked' => true]);
	}

	/**
	 * Revoke the given verification token
	 * 
	 * @param string $token
	 * @return void
	 **/
	public static function revokeVerifyToken($token): void{
		$verifyToken = Identity::verifyToken();

		$verifyToken::where('token', $token)->update(['revoked' => true]);
	}

	/**
	 * Set the token as used
	 * 
	 * @param string $token
	 * @return void
	 **/
	public static function useToken($token): void{
		if(!$token){
			throw new InvalidTokenException();
		}

		$token->used_at = now();
		$token->save();
	}
}