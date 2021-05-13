<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('orderRefNo');
            $table->boolean('is_promo_code')->default(false);
            $table->bigInteger('promo_id')->unsigned()->nullable();
            $table->foreign('promo_id')->references('id')->on('promocodes');
            $table->boolean('is_wallet_balance_used')->default(false);
            $table->decimal('wallet_balance_used',15,2)->default(0);
            $table->decimal('promo_discount',15,2)->default(0);
            $table->decimal('subTotal',15,2);
            $table->decimal('total',15,2);
            $table->decimal('shipping_charge',15,2);
            $table->string('paymentMode');
            $table->date('delivery_date')->nullable();
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
        Schema::dropIfExists('orders');
    }
}
