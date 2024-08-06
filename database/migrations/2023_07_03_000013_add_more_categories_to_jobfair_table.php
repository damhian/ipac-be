<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMoreCategoriesToJobfairTable extends Migration
{
    public function up()
    {
        Schema::table('jobfair', function (Blueprint $table) {
            $table->string('region', 255)->after('short_description');
            $table->string('company', 255)->after('region');
            $table->enum('jobtype', ['Harian', 'Magang', 'Waktu Penuh', 'Paruh Waktu', 'Kontrak'])->after('company');
            $table->string('jobtitle', 255)->after('jobtype');
        });
    }

    public function down()
    {
        Schema::table('jobfair', function (Blueprint $table) {
            $table->dropColumn('region');
            $table->dropColumn('company');
            $table->dropColumn('jobtype');
            $table->dropColumn('jobtitle');
        });
    }
}
