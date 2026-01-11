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
        Schema::table('athletes', function (Blueprint $table) {
            // Add competition_class_id foreign key
            $table->foreignId('competition_class_id')
                ->nullable()
                ->after('cabor_id')
                ->constrained()
                ->nullOnDelete();
        });

        // Note: Keeping the old competition_class column for now
        // to preserve existing data. Can be removed in a future migration.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('athletes', function (Blueprint $table) {
            $table->dropForeign(['competition_class_id']);
            $table->dropColumn('competition_class_id');
        });
    }
};
