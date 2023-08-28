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
            $table->integer('license_number')->nullable();
            $table->unsignedBigInteger('profile_image_id')->nullable();
            $table->string('first_name', 50);
            $table->string('last_name', 50)->nullable();
            $table->integer('tahun_masuk');
            $table->integer('tahun_lulus');
            $table->string('training_program', 80)->nullable();
            $table->string('batch', 25);
            $table->string('current_job')->nullable();
            $table->string('current_workplace', 255)->nullable();
            $table->string('birth_place', 80)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('nationality', 80);
            $table->string('address', 255)->nullable();
            $table->string('phone_number', 15)->nullable();
            $table->string('phone_number_code', 4)->nullable();
            $table->enum('gender', ['LAKI-LAKI', 'PEREMPUAN']);
            $table->enum('status', ['HIDUP', 'ALMARHUM', 'GUGUR DALAM TUGAS']);
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
