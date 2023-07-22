<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStrukturOrganisasiTable extends Migration
{
    public function up()
    {
        Schema::create('struktur_organisasi', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 60);
            $table->string('jabatan', 125);
            $table->string('image_url', 60);
            $table->unsignedBigInteger('created_by');
            $table->dateTime('created_at');
            $table->timestamp('updated_at')->useCurrent();

            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('struktur_organisasi');
    }
}
