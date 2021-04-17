<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('product_name');
            $table->text('description');
            $table->decimal('price',15,2);
            $table->decimal('mrp',15,2);
            $table->string('available_sizes');
            $table->string('available_colors');
            $table->string('primary_image');
            $table->decimal('avg_rating',15,2)->default(0);
            $table->boolean('is_on_sale')->default(false);
            $table->decimal('sale_price',15,2)->default(0);
            $table->decimal('sale_percentage',15,2)->default(0);
            $table->boolean('is_new')->default(true);
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
        Schema::dropIfExists('products');
    }
}
