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
        Schema::create('error_logs', function (Blueprint $table) {
            $table->id();
            
            // User context (nullable for unauthenticated errors)
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('user_name')->nullable();
            
            // Error categorization
            $table->string('type'); // validation, auth, server, database, etc.
            $table->string('severity')->default('error'); // info, warning, error, critical
            
            // Human-readable info
            $table->string('title'); // Short human-readable title
            $table->text('message'); // Human-readable error message
            
            // Technical details (for debugging)
            $table->string('exception_class')->nullable();
            $table->text('exception_message')->nullable();
            $table->string('file')->nullable();
            $table->integer('line')->nullable();
            $table->json('trace')->nullable(); // Stack trace (truncated)
            
            // Request context
            $table->string('url')->nullable();
            $table->string('method')->nullable(); // GET, POST, etc.
            $table->json('request_data')->nullable(); // Sanitized request data
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            
            // Resolution status
            $table->boolean('is_resolved')->default(false);
            $table->text('resolution_notes')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            
            $table->timestamp('created_at')->useCurrent();
            
            // Indexes
            $table->index('user_id');
            $table->index('type');
            $table->index('severity');
            $table->index('is_resolved');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('error_logs');
    }
};
