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
        Schema::create('event_athletes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('athlete_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cabor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('competition_class_id')->nullable()->constrained('competition_classes')->nullOnDelete();
            $table->enum('status', ['registered', 'verified', 'rejected'])->default('registered');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // UNIQUE: One athlete can register for multiple competition classes per event
            // But not the same combination of event + athlete + competition_class
            $table->unique(['event_id', 'athlete_id', 'competition_class_id'], 'event_athlete_class_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_athletes');
    }
};
