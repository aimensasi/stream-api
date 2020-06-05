<?php

namespace Modules\Identity\Entities;

use Illuminate\Database\Eloquent\Model;

class VerifyToken extends Model{

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'user_id', 'token', 'used_for', 'expire_in', 'used_at', 'revoked'
	];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
		// attributes
	];

	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'used_at' => 'datetime',
		'revoked' => 'boolean'
	];

	/**
	 * Identity used_for options
	 * 
	 **/
	public const EMAIL = 'Email';
	public const PASSWORD = 'Password';
	public const PHONE_NUMBER = 'PhoneNumber';

	/**
	 * Identity Types
	 * 
	 **/
	public const CODE = 'Code';
	public const TOKEN = 'token';



	/**
	 * Get expire_in in seconds 
	 * 
	 **/
	public function getExpireInSecondsAttribute(){
		return $this->expire_in->diffInSeconds();
	}

	/**
	 * Find By Token where not expired
	 * 
	 * @param string $token
	 * @return VerifyToken | null
	 **/
	public static function findByToken($token){
		return self::where('token', $token)
			->where('expire_in', '>', now())
			->first();
	}

	/**
	 * 
	 * Generate Identity for the given user and type
	 * 
	 * @param $userId
	 * @param string $used_for
	 * @param int $expire_in
	 * @return VerifyToken
	 **/
	public static function identity($userId, $used_for, $identity_type, $expire_in = 24){
		$token = null;
		$isTaken = true;

		while($isTaken){
			$token = $identity_type == self::CODE ? self::code() : self::token($used_for);
			$isTaken = self::where('token', $token)->first() != null;
		}

		return self::create([
			'user_id' => $userId,
			'token' => $token,
			'expire_in' => now()->addHours($expire_in),
			'used_for' => $used_for,
		]);
	}

	/**
	 * Revoke all identities for the given user
	 * 
	 * @param $userId
	 * @return void
	 **/
	public static function revoke_all_for($userId){
		self::where('user_id', $userId)
			->update(['revoked' => true]);
	}

	/**
	 * Revoke all users identities for the type
	 * 
	 * @param $userId
	 * @return void
	 **/
	public static function revoke_all_by_type($userId, $used_for){
		self::where('user_id', $userId)
			->where('used_for', $used_for)
			->update(['revoked' => true]);
	}

	/**
	 * Revoke identity
	 * 
	 * @param $userId
	 * @return void
	 **/
	public function revoke(){
		$this->revoked = true;
		$this->save();
	}

	/**
	 * 
	 * Generate Unique Identity Code 5 characters 
	 * 
	 * @return string
	 **/
	public static function code(){
		$code = '';
		$chars = 'A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z,0,1,2,3,4,5,6,7,8,9';
		$charArray = explode(',', $chars);
		shuffle($charArray);

		$code = $charArray[array_rand($charArray, 1)] .
			$charArray[array_rand($charArray, 1)] .
			$charArray[array_rand($charArray, 1)] .
			$charArray[array_rand($charArray, 1)] .
			$charArray[array_rand($charArray, 1)];

		return $code;
	}

	/**
	 * 
	 * Generate Unique Identity Token
	 * 
	 * @return string
	 **/
	public static function token($used_for){
		return md5($used_for . ' verification' . time() . 'token');
	}

	/**
	 * Get user
	 * 
	 * @return User
	 **/
	public function user(){
		return $this->belongsTo(User::class);
	}
}
