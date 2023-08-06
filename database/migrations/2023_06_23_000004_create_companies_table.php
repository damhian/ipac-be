<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('image_url', 255)->nullable();
            $table->string('name', 50);
            $table->text('about')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->dateTime('created_at');
            $table->timestamp('updated_at')->useCurrent();

            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('companies');
    }
}
