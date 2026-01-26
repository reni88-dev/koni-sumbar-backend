<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adds linked_to_reference_field column to form_fields table.
     * This allows select/radio fields to auto-populate from the reference record's relation.
     * Example: A Cabor dropdown can be linked to 'cabor_id' field from the Athlete reference.
     */
    public function up(): void
    {
        Schema::table('form_fields', function (Blueprint $table) {
            // Field from reference model to link to (e.g. 'cabor_id' from Athlete)
            $table->string('linked_to_reference_field')->nullable()->after('reference_field');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('form_fields', function (Blueprint $table) {
            $table->dropColumn('linked_to_reference_field');
        });
    }
};
