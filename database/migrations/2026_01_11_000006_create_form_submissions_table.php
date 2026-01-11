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
        Schema::create('form_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_template_id')->constrained()->cascadeOnDelete();
            
            // Reference to the selected model record (e.g., athlete_id)
            $table->unsignedBigInteger('reference_id')->nullable();
            
            // Which admin submitted this form
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            // Unique submission code
            $table->string('submission_code')->unique();
            
            // Additional metadata
            $table->json('meta')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Index for faster lookups
            $table->index(['form_template_id', 'reference_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_submissions');
    }
};
