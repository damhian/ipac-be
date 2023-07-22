<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMoreColumnsToUserProfilesTable extends Migration
{
    public function up()
    {

        Schema::table('user_profiles', function (Blueprint $table) {
          $table->integer('license_number', 30)->after('nomor_anggota');
          $table->integer('tahun_masuk', 4)->after('last_name');
          $table->string('training_program', 80)->after('tahun_lulus');
          $table->string('batch', 25)->after('training_program');
          $table->enum('current_job', ['121', '135', '91', '141', 'TNI', 'Polri', 'PNS', 'Pensiunan', 'Belum bekerja'])->after('batch');
          $table->string('current_workplace', 255)->after('current_job');
          $table->string('birth_place', 80)->after('current_job');
        });
    }

    public function down()
    {
        Schema::table('user_profiles', function (Blueprint $table) {
          $table->dropColumn('profile_image_id');
        });
    }
}
