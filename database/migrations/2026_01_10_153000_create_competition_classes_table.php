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
        Schema::create('competition_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cabor_id')->constrained()->onDelete('cascade');
            $table->string('name'); // e.g., "80kg", "U-21", "Senior"
            $table->string('code')->nullable(); // e.g., "A", "B", "C"
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            // Index for faster queries
            $table->index(['cabor_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competition_classes');
    }
};
