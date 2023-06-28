<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserExperiencesTable extends Migration
{
    public function up()
    {
        Schema::create('user_experiences', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('alumni_id');
            $table->unsignedBigInteger('company_id');
            $table->string('position', 20);
            $table->date('start_at');
            $table->date('end_at')->nullable();
            $table->dateTime('created_at');
            $table->timestamp('updated_at')->useCurrent();
            
            $table->foreign('alumni_id')->references('id')->on('users');
            $table->foreign('company_id')->references('id')->on('companies');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_experiences');
    }
}
