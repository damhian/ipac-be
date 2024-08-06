<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTahunLulusToUserProfilesTable extends Migration
{
    public function up()
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->integer('tahun_lulus')->nullable()->after('last_name');
        });
    }

    public function down()
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn('tahun_lulus');
        });
    }
}