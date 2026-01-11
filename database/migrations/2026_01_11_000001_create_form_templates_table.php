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
        Schema::create('form_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            
            // Reference model for auto-fill (e.g., App\Models\Athlete)
            $table->string('reference_model')->nullable();
            $table->string('reference_display_field')->default('name');
            
            // Status
            $table->boolean('is_active')->default(true);
            
            // Additional settings (JSON)
            $table->json('settings')->nullable();
            
            // Who created this form
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_templates');
    }
};
