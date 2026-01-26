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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            
            // User who performed the action (nullable for system/guest actions)
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('user_name')->nullable(); // Snapshot of user name
            
            // Action type
            $table->enum('action', ['created', 'updated', 'deleted', 'restored']);
            
            // Model information
            $table->string('model_type'); // Full class name (App\Models\Athlete)
            $table->string('model_name'); // Short name (Athlete)
            $table->unsignedBigInteger('model_id');
            $table->string('record_name')->nullable(); // Snapshot of record name/title
            
            // Data changes
            $table->json('old_values')->nullable(); // Data before change
            $table->json('new_values')->nullable(); // Data after change
            $table->json('changed_fields')->nullable(); // Array of changed field names
            
            // Request context
            $table->string('ip_address', 45)->nullable(); // IPv6 compatible
            $table->text('user_agent')->nullable();
            $table->string('url')->nullable();
            
            $table->timestamp('created_at')->useCurrent();
            
            // Indexes for common queries
            $table->index('user_id');
            $table->index('model_type');
            $table->index('action');
            $table->index('created_at');
            $table->index(['model_type', 'model_id']); // For finding all logs of a specific record
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
