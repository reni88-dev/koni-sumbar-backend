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
        Schema::create('form_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_section_id')->constrained()->cascadeOnDelete();
            
            // Basic field info
            $table->string('label');
            $table->string('name'); // field name for form data
            $table->string('placeholder')->nullable();
            
            // Field type
            $table->enum('type', [
                'text',
                'textarea', 
                'number',
                'email',
                'date',
                'time',
                'select',
                'radio',
                'checkbox',
                'file',
                'model_reference',  // auto-fill from selected model record
                'calculated'        // computed from other fields
            ])->default('text');
            
            // For table section: grouping and sub-labels
            $table->string('group_label')->nullable(); // "KEKUATAN", "POWER"
            $table->string('sub_label')->nullable();   // "Otot Perut", "Otot Lengan"
            $table->string('technique')->nullable();   // "Sit up", "Push up"
            
            // Unit suffix for number inputs
            $table->string('unit')->nullable(); // "Kali", "cm", "dtk"
            
            // Validation
            $table->boolean('is_required')->default(false);
            $table->json('validation_rules')->nullable();
            
            // Ordering
            $table->integer('order')->default(0);
            
            // Data source for select/radio (model or custom options)
            $table->enum('data_source_type', ['model', 'custom'])->nullable();
            $table->string('data_source_model')->nullable(); // "athlete", "cabor", etc
            $table->string('data_source_value_field')->nullable(); // "id"
            $table->string('data_source_label_field')->nullable(); // "name"
            $table->json('data_source_filters')->nullable();
            
            // For model_reference type: which field to pull from reference model
            $table->string('reference_field')->nullable(); // "name", "birth_info", "height"
            $table->boolean('is_readonly')->default(false);
            
            // For calculated type: formula and dependencies
            $table->text('calculation_formula')->nullable();
            $table->json('calculation_dependencies')->nullable(); // ["tinggi_badan", "berat_badan"]
            
            // Has auto-grading (kategori based on score)
            $table->boolean('has_grading')->default(false);
            
            // Additional settings
            $table->json('settings')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_fields');
    }
};
