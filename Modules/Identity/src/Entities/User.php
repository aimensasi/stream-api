<?php

namespace Modules\Identity\Entities;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable{
	use Notifiable, HasApiTokens;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'email', 'password', 'profile'
	];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
		'password',
	];

	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'profile' => 'array',
	];


	/**
	 * User Roles
	 *  
	 */
	public function roles(){
		return $this->belongsToMany(Role::class, 'user_roles');
	}

	/**
	 * User Roles
	 *  
	 */
	public function services(){
		return $this->hasMany(Service::class);
	}

	public function hasServiceWithProvider($provider){
		return $this->services->first() && $this->services->first()->provider == $provider;
	}

	/**
	 * Is user account verified 
	 * 
	 **/
	public function getIsEmailVerifiedAttribute(){
		$profile = $this->profile;
		
		if(!$profile){
			return true;
		}

		$email_verified_at = @$profile['email_verified_at'];
		$must_verify_email = @$profile['must_verify_email'];

		return $email_verified_at || !$must_verify_email;
	}

	/**
	 * Is user account verified 
	 * 
	 **/
	public function getIsPhoneVerifiedAttribute(){
		$profile = $this->profile;

		if (!$profile) {
			return true;
		}

		$phone_number_verified_at = @$profile['phone_number_verified_at'];
		$must_verify_phone_number = @$profile['must_verify_phone_number'];

		return $phone_number_verified_at || !$must_verify_phone_number;
	}
}
