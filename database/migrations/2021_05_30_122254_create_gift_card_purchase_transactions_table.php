<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGiftCardPurchaseTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gift_card_purchase_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->enum('payment_mode', ['razorpay', 'paytm']);
            $table->unsignedBigInteger('user_gift_card_id');
            $table->foreign('user_gift_card_id')->references('id')->on('user_gift_cards');
            $table->string('gateway_transaction_id')->nullable();
            $table->tinyInteger('payment_status')->default(0)
                ->comment('0=UNPAID | 1=PAID ');
            $table->decimal('amount',15,2);
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
        Schema::dropIfExists('gift_card_purchase_transactions');
    }
}
