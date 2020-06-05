<?php

namespace Modules\Identity\Repositories;

use Modules\Identity\Entities\User;
use Modules\Identity\Identity;
use Illuminate\Support\Facades\Hash;

class UserRepository {


	public static function __callStatic($method, $arguments){
		return false;
	}


	/**
	 * Create user from attributes
	 * 
	 * @param array $attributes
	 * @return \Modules\Identity\Entities\User
	 */
	public static function create($attributes, $roleId){
		$password = @$attributes['password'];


		if($password){
			$attributes['password'] = Hash::make($password);
		}
		
		$user = Identity::user();
		$user = $user::create($attributes);

		$user->roles()->attach($roleId);

		return $user;
	}


	/**
	 * Get a user by the given ID.
	 *
	 * @param  int  $id
	 * @return \Modules\Identity\Entities\User|null
	 */
	public static function find($id){
		$user = Identity::user();

		return $user->where($user->getKeyName(), $id)->first();
	}


	/**
	 * Find user by email and role id 
	 * 
	 * @param string $email
	 * @param int $roleId
	 * @return \Modules\Identity\Entities\User|null
	 **/
	public static function findByEmailAndRoleId($email, $roleId){
		$user = Identity::user();

		return $user->where('email', $email)
			->whereHas('roles', function ($query) use ($roleId) {
				$query->where('roles.id', $roleId);
			})->first();
	}


	/**
	 * Verify Account for the given user 
	 * 
	 * @param $userId
	 * @return User
	 **/
	public static function verifyAccount($userId): User{
		$user = self::find($userId);

		$profile = $user->profile;
		$profile['email_verified_at'] = now();
		$user->profile = $profile;
		$user->save();

		return $user;
	}


	/**
	 * Reset password for the given user 
	 * 
	 * @param $userId
	 * @return User
	 **/
	public static function resetPassword($userId, $password): User{
		$user = self::find($userId);

		$user->password = Hash::make($password);
		$user->save();

		return $user;
	}


	/**
	 * Verify Phone Number for the given user 
	 * 
	 * @param $userId
	 * @return User
	 **/
	public static function verifyPhoneNumber($userId): User{
		$user = self::find($userId);

		$profile = $user->profile;
		$profile['phone_number_verified_at'] = now();
		$user->profile = $profile;
		$user->save();

		return $user;
	}

	/**
	 * Update the user profile
	 * 
	 * @param int $userId
	 * @param array $profile
	 * 
	 * @return User
	 **/
	public static function updateUserProfile($userId, $profile): User{
		$user = self::find($userId);

		$oldProfile = $user->profile;
		$profile = array_merge($oldProfile, $profile);
		$user->profile = $profile;
		$user->save();

		return $user;
	}

}
