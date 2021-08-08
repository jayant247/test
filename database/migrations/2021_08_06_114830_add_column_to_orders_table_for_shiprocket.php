<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToOrdersTableForShiprocket extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('height',15,2)->nullable();
            $table->decimal('breadth',15,2)->nullable();
            $table->decimal('length',15,2)->nullable();
            $table->decimal('weight',15,2)->nullable();
            $table->string('shipping_status')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->string('shiprocker_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('height');
            $table->dropColumn('breadth');
            $table->dropColumn('length');
            $table->dropColumn('weight');
            $table->dropColumn('shipping_status');
            $table->dropColumn('shipped_at');
            $table->dropColumn('shiprocker_id');
        });
    }
}
