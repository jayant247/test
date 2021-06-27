<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromocodeHasPincodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promocode_has_pincodes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('promo_id')->unsigned();
            $table->foreign('promo_id')->references('id')->on('promocodes');
            $table->bigInteger('pincode_id')->unsigned();
            $table->foreign('pincode_id')->references('id')->on('delivery_pincodes');
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
        Schema::dropIfExists('promocode_has_pincodes');
    }
}
