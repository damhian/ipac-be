<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    public function up()
    {   
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title', 50);
            $table->text('content');
            $table->string('image', 255)->nullable();
            $table->string('short_description', 255);
            $table->string('location_name', 50);
            $table->double('location_lon');
            $table->double('location_lat');
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->enum('type', ['event', 'news']);
            $table->unsignedBigInteger('created_by');
            $table->dateTime('created_at');
            $table->timestamp('updated_at')->useCurrent();
            $table->enum('status', ['pending', 'approved', 'denied', 'deleted']);
            
            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('events');
    }
}
