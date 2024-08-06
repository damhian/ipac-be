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
        // Update the status enum values
        DB::statement("ALTER TABLE users MODIFY status ENUM('pending', 'approved', 'rejected', 'deleted')");

        // Change current_status to VARCHAR
        Schema::table('users', function (Blueprint $table) {
            $table->string('current_status', 60)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // To reverse the changes, revert the enum values and change current_status back to enum
        DB::statement("ALTER TABLE users MODIFY status ENUM('pending', 'approved', 'denied', 'deleted')");

        // Change current_status back to ENUM
        Schema::table('users', function (Blueprint $table) {
            $table->enum('current_status', ['HIDUP', 'ALMARHUM', 'GUGUR DALAM TUGAS'])->change();
        });
    }
};
