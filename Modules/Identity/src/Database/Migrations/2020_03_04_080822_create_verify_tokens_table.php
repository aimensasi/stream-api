<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVerifyTokensTable extends Migration{

	/**
   * Run the migrations.
   *
   * @return void
   */
  public function up(){
    Schema::create('verify_tokens', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->unsignedBigInteger('user_id');
      $table->string('token')->unique();
      $table->string('used_for');
      $table->timestamp('expire_in');
      $table->timestamp('used_at')->nullable();
      $table->boolean('revoked')->default(false);
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down(){
    Schema::dropIfExists('verify_tokens');
  }
}
