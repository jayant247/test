<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGatewayOrderKeyInTheOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->text('gateway_transaction_id')->nullable();
            $table->unsignedBigInteger('gift_card_id')->nullable();
            $table->decimal('gift_card_amount_used')->default(0);
            $table->foreign('gift_card_id')->references('id')->on('user_gift_cards');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('=orders', function (Blueprint $table) {
            //
        });
    }
}
