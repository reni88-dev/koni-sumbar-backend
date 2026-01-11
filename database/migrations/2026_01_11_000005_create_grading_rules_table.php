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
        // Grading rules for auto-categorizing scores
        // e.g., Sit up score 40+ for male age 17-25 = "Baik Sekali"
        Schema::create('grading_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_field_id')->constrained()->cascadeOnDelete();
            
            // Conditions
            $table->enum('gender', ['male', 'female', 'all'])->default('all');
            $table->integer('age_min')->nullable();
            $table->integer('age_max')->nullable();
            
            // Score range
            $table->decimal('score_min', 10, 2);
            $table->decimal('score_max', 10, 2);
            
            // Result category
            $table->string('category'); // "Baik Sekali", "Baik", "Sedang", "Kurang"
            
            // Order for evaluation (first match wins)
            $table->integer('order')->default(0);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grading_rules');
    }
};
