<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->text('user_type');
            $table->text('notification_type');
            $table->string('heading');
            $table->text('mobile_body')->nullable();
            $table->text('mail_body')->nullable();
            $table->text('sms_body')->nullable();
            $table->string('mobile_image')->nullable();
            $table->timestamp('registered_from')->nullable();
            $table->timestamp('registered_till')->nullable();
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
        Schema::dropIfExists('notifications');
    }
}
