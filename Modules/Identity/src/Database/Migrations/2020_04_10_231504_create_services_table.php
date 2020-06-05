<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServicesTable extends Migration{

	/**
   * Run the migrations.
   *
   * @return void
   */
  public function up(){
    Schema::create('services', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->text('access_token')->nullable();
      $table->text('refresh_token')->nullable();
      $table->string('expires_in');
      $table->string('provider');
      $table->string('provider_id')->nullable();
      $table->unsignedBigInteger('user_id');
      $table->string('jarvis');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down(){
    Schema::dropIfExists('services');
  }
}
