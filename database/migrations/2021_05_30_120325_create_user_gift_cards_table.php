<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserGiftCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_gift_cards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('coupon_code');
            $table->string('gift_for_mobile_number');
            $table->timestamp('expiry_date');
            $table->integer('withdraw_otp')->nullable();
            $table->timestamp('withdraw_otp_time')->nullable();
            $table->tinyInteger('use_status')->default(0)
                ->comment('0=UNUSED | 1=USED  ');
            $table->unsignedBigInteger('gift_card_id');
            $table->tinyInteger('payment_status')->default(0)
                ->comment('0=UNPAID | 1=PAID ');
            $table->foreign('gift_card_id')->references('id')->on('gift_cards');
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
        Schema::dropIfExists('user_gift_cards');
    }
}
