<?php

namespace Modules\Identity\Entities;

use Illuminate\Database\Eloquent\Model;

class Role extends Model{

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		"name"
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
		// attributes
	];


	/**
	 * User Roles
	 *  
	 */
	public function users(){
		return $this->belongsToMany(User::class, 'user_roles');
	}
}
