<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReferenceTable extends Migration
{
    public function up()
    {
        Schema::create('reference', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('value');
            $table->string('item_type');
            $table->unsignedBigInteger('created_by');
            $table->dateTime('created_at');
            $table->timestamp('updated_at')->useCurrent();
            
            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('reference');
    }
}
