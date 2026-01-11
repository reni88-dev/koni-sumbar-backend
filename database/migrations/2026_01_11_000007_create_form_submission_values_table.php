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
        Schema::create('form_submission_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_submission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('form_field_id')->constrained()->cascadeOnDelete();
            
            // The actual submitted value
            $table->text('value')->nullable();
            
            // For fields with grading: the calculated category
            $table->string('calculated_category')->nullable();
            
            // For file uploads
            $table->json('file_info')->nullable();
            
            $table->timestamps();
            
            // Index for lookups
            $table->index(['form_submission_id', 'form_field_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_submission_values');
    }
};
