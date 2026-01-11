<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adds nik_hash column for blind index - allows unique checking
     * without decrypting all NIK values.
     */
    public function up(): void
    {
        Schema::table('athletes', function (Blueprint $table) {
            // Hash column for fast unique lookups
            $table->string('nik_hash', 64)->nullable()->unique()->after('nik');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('athletes', function (Blueprint $table) {
            $table->dropColumn('nik_hash');
        });
    }
};
