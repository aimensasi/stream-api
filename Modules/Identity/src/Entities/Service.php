<?php

namespace Modules\Identity\Entities;

use Illuminate\Database\Eloquent\Model;

class Service extends Model{

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'access_token', 'refresh_token', 'expires_in', 'provider', 'provider_id', 'user_id', 'jarvis'
	];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
		'jarvis'
	];

	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		// attributes
	];

	public function user(){
		return $this->belongsTo(User::class);
	}
}
