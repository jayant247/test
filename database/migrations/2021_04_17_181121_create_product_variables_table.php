<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductVariablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_variables', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('product_id')->unsigned();
            $table->foreign('product_id')->references('id')->on('products');
            $table->decimal('price',15,2);
            $table->decimal('mrp',15,2);
            $table->string('color')->nullable();
            $table->string('size')->nullable();
            $table->string('primary_image');
            $table->boolean('is_on_sale')->default(false);
            $table->decimal('sale_price',15,2)->default(0);
            $table->decimal('sale_percentage',15,2)->default(0);
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
        Schema::dropIfExists('product_variables');
    }
}
