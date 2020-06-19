<?php

namespace App\Models;

use Modules\Identity\Identity;
use Illuminate\Database\Eloquent\Model;

class Post extends Model{

	/**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
		'user_id', 'type', 'caption'
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
  
  public function user(){
    return $this->belongsTo(Identity::userModel());
  }
}
