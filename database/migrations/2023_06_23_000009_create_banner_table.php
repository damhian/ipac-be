<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBannerTable extends Migration
{
    public function up()
    {
        Schema::create('banner', function (Blueprint $table) {
            $table->id();
            $table->string('title', 50)->nullable();
            $table->text('content')->nullable();
            $table->string('short_description', 255)->nullable();
            $table->string('file_url', 255);
            $table->enum('type', ['home', 'profile', 'store', 'about']);
            $table->unsignedBigInteger('created_by');
            $table->dateTime('created_at');
            $table->timestamp('updated_at')->useCurrent();
            $table->enum('status', ['active', 'deleted']);
            
            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('banner');
    }
}
