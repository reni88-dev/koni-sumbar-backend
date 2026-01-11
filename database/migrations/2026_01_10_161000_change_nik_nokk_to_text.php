<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('athletes', function (Blueprint $table) {
            // Change nik and no_kk to TEXT to accommodate encrypted data
            $table->text('nik')->nullable()->change();
            $table->text('no_kk')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('athletes', function (Blueprint $table) {
            $table->string('nik', 16)->nullable()->change();
            $table->string('no_kk', 16)->nullable()->change();
        });
    }
};
