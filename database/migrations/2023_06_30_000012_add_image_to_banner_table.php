<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddImageToBannerTable extends Migration
{
    public function up()
    {
        Schema::table('banner', function (Blueprint $table) {
            $table->string('image', 255)->after('short_description');
        });
    }

    public function down()
    {
        Schema::table('banner', function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }
}
