<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNomorAnggotaToUserProfilesTable extends Migration
{
    public function up()
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->string('nomor_anggota', 8)->after('alumni_id');
        });
    }

    public function down()
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn('nomor_anggota');
        });
    }
}
