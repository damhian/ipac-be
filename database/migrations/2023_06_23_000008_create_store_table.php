<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoreTable extends Migration
{
    public function up()
    {
        Schema::create('store', function (Blueprint $table) {
            $table->id();
            $table->string('title', 50);
            $table->text('content')->nullable();
            $table->string('short_description', 255);
            $table->decimal('price', 8, 2);
            $table->unsignedBigInteger('created_by');
            $table->dateTime('created_at');
            $table->timestamp('updated_at')->useCurrent();
            $table->enum('status', ['pending', 'approved', 'denied', 'deleted']);
            
            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('store');
    }
}
