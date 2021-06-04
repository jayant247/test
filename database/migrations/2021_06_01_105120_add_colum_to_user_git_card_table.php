<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumToUserGitCardTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_gift_cards', function (Blueprint $table) {
            $table->unsignedBigInteger('gift_for_user_id')->nullable();
            $table->foreign('gift_for_user_id')->references('id')->on('users');
            $table->timestamp('otp_verified_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_gift_cards', function (Blueprint $table) {
            //
        });
    }
}
