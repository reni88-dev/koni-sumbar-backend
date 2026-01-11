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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name');                                   // Nama event
            $table->string('slug')->unique();                         // URL-friendly
            $table->enum('type', ['provincial', 'national', 'international'])->default('provincial');
            $table->year('year');                                     // Tahun pelaksanaan
            $table->string('location')->nullable();                   // Lokasi event
            $table->date('start_date')->nullable();                   // Tanggal mulai
            $table->date('end_date')->nullable();                     // Tanggal selesai
            $table->text('description')->nullable();                  // Deskripsi
            $table->string('logo')->nullable();                       // Path to logo
            $table->date('registration_start')->nullable();           // Buka pendaftaran
            $table->date('registration_end')->nullable();             // Tutup pendaftaran
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
