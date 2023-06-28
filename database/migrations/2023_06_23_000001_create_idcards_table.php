<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIdCardsTable extends Migration
{
    public function up()
    {
        Schema::create('id_cards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('alumni_id');
            $table->string('image_url', 255);
            $table->dateTime('created_at');
            $table->timestamp('updated_at')->useCurrent();
            
            $table->foreign('alumni_id')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('id_cards');
    }
}
