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
        Schema::create('form_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_template_id')->constrained()->cascadeOnDelete();
            
            $table->string('title'); // "DATA DIRI", "HASIL TES KONDISI FISIK"
            $table->enum('type', ['normal', 'table'])->default('normal');
            $table->integer('order')->default(0);
            
            // For table type: column definitions
            $table->json('table_columns')->nullable(); // ["Komponen", "Teknik", "Skor", "Kategori"]
            
            // Additional settings
            $table->json('settings')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_sections');
    }
};
