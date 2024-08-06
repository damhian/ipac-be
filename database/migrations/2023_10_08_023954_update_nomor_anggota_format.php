<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing records
        DB::table('user_profiles')
            ->whereNotNull('tahun_lulus')
            ->whereNotNull('id')
            ->whereRaw('(CHAR_LENGTH(nomor_anggota) = 4 OR CHAR_LENGTH(nomor_anggota) = 5)')
            ->update(['nomor_anggota' => DB::raw('CONCAT(tahun_lulus, LPAD(id, 3, "0"))')]);

        // Modify the column to match the new format
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->string('nomor_anggota', 7)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert changes to the original format
        DB::table('user_profiles')
            ->whereNotNull('tahun_lulus')
            ->whereNotNull('id')
            ->whereRaw('(CHAR_LENGTH(nomor_anggota) = 4 OR CHAR_LENGTH(nomor_anggota) = 5)')
            ->update(['nomor_anggota' => DB::raw('LEFT(nomor_anggota, 4)')]);

        // Modify the column back to the original format
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->string('nomor_anggota', 4)->change();
        });
    }
};
