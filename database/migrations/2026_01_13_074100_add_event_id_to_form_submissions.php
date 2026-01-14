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
        Schema::table('form_submissions', function (Blueprint $table) {
            $table->foreignId('event_id')
                  ->nullable()
                  ->after('form_template_id')
                  ->constrained()
                  ->nullOnDelete();
            
            // Index for faster lookups by event
            $table->index(['form_template_id', 'event_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('form_submissions', function (Blueprint $table) {
            $table->dropForeign(['event_id']);
            $table->dropIndex(['form_template_id', 'event_id']);
            $table->dropColumn('event_id');
        });
    }
};
