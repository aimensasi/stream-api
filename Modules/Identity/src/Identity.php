<?php

namespace Modules\Identity;

use BadMethodCallException;
use Illuminate\Support\Facades\Auth;

use Modules\Identity\Repositories\UserRepository;
use Modules\Identity\Repositories\RoleRepository;
use Modules\Identity\Repositories\ServiceRepository;
use Modules\Identity\Repositories\PassportRepository;
use Modules\Identity\Repositories\VerifyTokenRepository;

class Identity {

	/**
	 * The user model class name.
	 *
	 * @var string
	 */
	public static $userModel = 'Modules\Identity\Entities\User';

	/**
	 * The role model class name.
	 *
	 * @var string
	 */
	public static $roleModel = 'Modules\Identity\Entities\Role';

	/**
	 * The verify token model class name.
	 *
	 * @var string
	 */
	public static $verifyTokenModel = 'Modules\Identity\Entities\VerifyToken';

	/**
	 * The service model class name.
	 *
	 * @var string
	 */
	public static $serviceModel = 'Modules\Identity\Entities\Service';


	public static function __callStatic($method, $arguments){
		$roleRepositoryResult = forward_static_call([RoleRepository::class, $method], $arguments);
		$userRepositoryResult = forward_static_call([UserRepository::class, $method], $arguments);
		$verifyTokenRepositoryResult = forward_static_call([VerifyTokenRepository::class, $method], $arguments);

		if($roleRepositoryResult !== false){
			return $roleRepositoryResult;
		}elseif($userRepositoryResult !== false){
			return $userRepositoryResult;
		}elseif($verifyTokenRepositoryResult !== false){
			return $verifyTokenRepositoryResult;
		}else{
			throw new BadMethodCallException("Undefined Function method $method");
		}
	}

	/**
	 * Get the user model class name.
	 *
	 * @return string
	 */
	public static function userModel(){
		return static::$userModel;
	}

	/**
	 * Get a new user model instance.
	 *
	 * @return \Modules\Identity\Entities\User
	 */
	public static function user(){
		return new static::$userModel;
	}

	/**
	 * Get the role model class name.
	 *
	 * @return string
	 */
	public static function roleModel(){
		return static::$roleModel;
	}

	/**
	 * Get a new role model instance.
	 *
	 * @return \Modules\Identity\Entities\Role
	 */
	public static function role(){
		return new static::$roleModel;
	}

	/**
	 * Get the verify token model class name.
	 *
	 * @return string
	 */
	public static function verifyTokenModel(){
		return static::$verifyTokenModel;
	}

	/**
	 * Get a new verify token model instance.
	 *
	 * @return \Modules\Identity\Entities\VerifyToken
	 */
	public static function verifyToken(){
		return new static::$verifyTokenModel;
	}

	/**
	 * Get the service model class name.
	 *
	 * @return string
	 */
	public static function serviceModel(){
		return static::$serviceModel;
	}

	/**
	 * Get a new service model instance.
	 *
	 * @return \Modules\Identity\Entities\Service
	 */
	public static function service(){
		return new static::$serviceModel;
	}

	/**
	 * Get currently Authenticated User.
	 *
	 * @return \Modules\Identity\Entities\User
	 */
	public static function currentUser($guard = null){
		return Auth::guard($guard)->user();
	}

	/**
	 * Create user from attributes.
	 *
	 * @param array $attributes
	 * @param int $roleId
	 * @return \Modules\Identity\Entities\User
	 */
	public static function createUser($attributes, $roleId){
		return UserRepository::create($attributes, $roleId);
	}

	/**
	 * Find user by email and role id 
	 * 
	 * @param string $email
	 * @param int $roleId
	 * @return \Modules\Identity\Entities\User|null
	 **/
	public static function findUserByEmailAndRoleId($email, $roleId){
		return UserRepository::findByEmailAndRoleId($email, $roleId);
	}

	/**
	 * Update the user profile
	 * 
	 * @param int $userId
	 * @param array $profile
	 * 
	 * @return \Modules\Identity\Entities\User|null
	 **/
	public static function updateUserProfile($userId, $profile){
		return UserRepository::updateUserProfile($userId, $profile);
	}

	/**
	 * Verify account for the specified user
	 *
	 * @param array $attributes
	 * @return void
	 */
	public static function verifyAccount($token){
		$token = VerifyTokenRepository::verifyTokenForEmail($token);

		VerifyTokenRepository::useToken($token);

		return UserRepository::verifyAccount($token->user_id);
	}

	/**
	 * Reset password for the specified user
	 *
	 * @param array $attributes
	 * @return \Modules\Identity\Entities\User
	 */
	public static function resetPassword($token, $password){
		$token = VerifyTokenRepository::verifyTokenForPassword($token);

		VerifyTokenRepository::useToken($token);

		return UserRepository::resetPassword($token->user_id, $password);
	}

		/**
	 * Verify Phone number for the specified user
	 *
	 * @param array $attributes
	 * @return \Modules\Identity\Entities\User
	 */
	public static function verifyPhoneNumber($token){
		$token = VerifyTokenRepository::verifyTokenForPhoneNumber($token);

		VerifyTokenRepository::useToken($token);

		return UserRepository::verifyPhoneNumber($token->user_id);
	}

	/**
	 * Create verify token from attributes.
	 *
	 * @param array $attributes
	 * @param int $userId
	 * @return \Modules\Identity\Entities\VerifyToken
	 */
	public static function createVerifyToken($attribute, $userId){
		return VerifyTokenRepository::createVerifyToken($attribute, $userId);
	}

	/**
	 * Revoke verify token for the specified user
	 *
	 * @param int $userId
	 * @return void
	 */
	public static function revokeVerifyTokensFor($userId){
		return VerifyTokenRepository::revokeVerifyTokensFor($userId);
	}


	/**
	 * Revoke verify token for the specified user
	 *
	 * @param string $token
	 * @return void
	 */
	public static function revokeVerifyToken($token){
		return VerifyTokenRepository::revokeVerifyToken($token);
	}


	/**
	 * Create access token from attributes.
	 *
	 * @param array $attributes
	 * @return \Illuminate\Http\Response
	 */
	public static function createAccessToken($attribute){
		return PassportRepository::createAccessToken($attribute);
	}

	/**
	 * Refresh the user access token.
	 *
	 * @param array $attributes
	 * @return \Illuminate\Http\Response
	 */
	public static function refreshAccessToken($attribute){
		return PassportRepository::refreshAccessToken($attribute);
	}


	/**
	 * Revoke User access.
	 *
	 * @param array $attribute
	 * @return void
	 */
	public static function revokeUserAccess($attribute){
		return PassportRepository::revokeUserAccess($attribute);
	}

	/**
	 * Attach third party oauth service to the user.
	 *
	 * @param int $userId
	 * @param array $attribute
	 * @return void
	 */
	public static function attachUserServices($userId, $attribute){
		return ServiceRepository::attachService($userId, $attribute);
	}
}