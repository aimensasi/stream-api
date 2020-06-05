<?php

namespace Modules\Identity\Database\Seeders;

use Modules\Identity\Entities\Role;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run(){
    Role::truncate();

    $roles = config('app.roles');

    if(!$roles || empty($roles)){
      $roles = ['Admin'];
    }

    foreach($roles as $role){
      Role::create(['name' => $role]);
    } 
  }
}
