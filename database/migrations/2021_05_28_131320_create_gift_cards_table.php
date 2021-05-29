<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGiftCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gift_cards', function (Blueprint $table) {
            $table->id();
            $table->softDeletes();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->decimal('purchase_amount',15,2);
            $table->decimal('gift_amount',15,2);
            $table->integer('validity_days_from_purchase_date');
            $table->timestamp('start_from')->default(now());
            $table->timestamp('end_on')->default(now());
            $table->boolean('is_active')->default(true);
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
        Schema::dropIfExists('gift_cards');
    }
}
