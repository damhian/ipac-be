<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserProfilesTable extends Migration
{
    public function up()
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('alumni_id');
            $table->string('nomor_anggota', 8);
            $table->unsignedBigInteger('profile_image_id');
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->date('date_of_birth');
            $table->string('address', 255)->nullable();
            $table->string('phone_number', 15);
            $table->string('phone_number_code', 4);
            $table->string('gender', 6);
            $table->dateTime('created_at');
            $table->timestamp('updated_at')->useCurrent();
            $table->foreign('alumni_id')->references('id')->on('users');
            $table->foreign('profile_image_id')->references('id')->on('user_gallery');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_profiles');
    }
}
