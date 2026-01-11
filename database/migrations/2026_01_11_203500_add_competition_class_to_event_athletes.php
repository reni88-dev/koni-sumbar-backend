<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Add column first
        if (!Schema::hasColumn('event_athletes', 'competition_class_id')) {
            Schema::table('event_athletes', function (Blueprint $table) {
                $table->foreignId('competition_class_id')
                      ->nullable()
                      ->after('cabor_id')
                      ->constrained('competition_classes')
                      ->nullOnDelete();
            });
        }
        
        // Step 2: Try to drop old unique index
        try {
            DB::statement('ALTER TABLE event_athletes DROP INDEX event_athlete_unique');
        } catch (\Exception $e) {
            // Index might not exist, that's ok
        }
        
        // Step 3: Add new unique index if not exists
        try {
            DB::statement('CREATE UNIQUE INDEX event_athlete_class_unique ON event_athletes (event_id, athlete_id, competition_class_id)');
        } catch (\Exception $e) {
            // Index might already exist
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop new unique constraint
        try {
            DB::statement('ALTER TABLE event_athletes DROP INDEX event_athlete_class_unique');
        } catch (\Exception $e) {
            // Ignore
        }
        
        Schema::table('event_athletes', function (Blueprint $table) {
            // Drop foreign key and column
            if (Schema::hasColumn('event_athletes', 'competition_class_id')) {
                $table->dropForeign(['competition_class_id']);
                $table->dropColumn('competition_class_id');
            }
        });
        
        // Restore old unique constraint
        try {
            DB::statement('CREATE UNIQUE INDEX event_athlete_unique ON event_athletes (event_id, athlete_id)');
        } catch (\Exception $e) {
            // Ignore
        }
    }
};
