<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('mobile_no')->unique();
            $table->text('firebase_token')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->text('primary_address')->nullable();
            $table->integer('primary_pincode')->nullable();
            $table->string('city')->nullable();
            $table->decimal('amount_spent',5,2)->default(0);
            $table->timestamp('last_ordered_at')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->text('wallet_key')->nullable();
            $table->string('password')->nullable();
            $table->integer('mobile_otp')->nullable();
            $table->integer('email_otp')->nullable();
            $table->timestamp('mobile_otp_time')->nullable();
            $table->timestamp('email_otp_time')->nullable();
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
