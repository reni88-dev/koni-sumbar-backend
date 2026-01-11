<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormField extends Model
{
    protected $fillable = [
        'form_section_id',
        'label',
        'name',
        'placeholder',
        'type',
        'group_label',
        'sub_label',
        'technique',
        'unit',
        'is_required',
        'validation_rules',
        'order',
        'data_source_type',
        'data_source_model',
        'data_source_value_field',
        'data_source_label_field',
        'data_source_filters',
        'reference_field',
        'is_readonly',
        'calculation_formula',
        'calculation_dependencies',
        'has_grading',
        'settings',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_readonly' => 'boolean',
        'has_grading' => 'boolean',
        'validation_rules' => 'array',
        'data_source_filters' => 'array',
        'calculation_dependencies' => 'array',
        'settings' => 'array',
    ];

    /**
     * Get the section this field belongs to.
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(FormSection::class, 'form_section_id');
    }

    /**
     * Get custom options for select/radio/checkbox.
     */
    public function options(): HasMany
    {
        return $this->hasMany(FormFieldOption::class)->orderBy('order');
    }

    /**
     * Get grading rules for this field.
     */
    public function gradingRules(): HasMany
    {
        return $this->hasMany(GradingRule::class)->orderBy('order');
    }

    /**
     * Get options for this field (from model or custom).
     */
    public function getOptionsData(): array
    {
        if ($this->data_source_type === 'model') {
            return $this->getModelOptions();
        }

        // For custom options (data_source_type is null, empty, or 'custom')
        // Return options from the form_field_options table
        if ($this->options->isNotEmpty()) {
            return $this->options->map(fn($o) => [
                'value' => $o->value,
                'label' => $o->label,
            ])->toArray();
        }

        return [];
    }

    /**
     * Get options from a model.
     */
    protected function getModelOptions(): array
    {
        $modelClass = FormTemplate::$allowedModels[$this->data_source_model] ?? null;
        
        if (!$modelClass || !class_exists($modelClass)) {
            return [];
        }

        $query = $modelClass::query();

        // Apply filters if any
        if ($this->data_source_filters) {
            foreach ($this->data_source_filters as $filter) {
                $query->where($filter['field'], $filter['operator'] ?? '=', $filter['value']);
            }
        }

        return $query->get()->map(fn($item) => [
            'value' => $item->{$this->data_source_value_field ?? 'id'},
            'label' => $item->{$this->data_source_label_field ?? 'name'},
        ])->toArray();
    }

    /**
     * Check if this field is a dropdown-type.
     */
    public function isSelectable(): bool
    {
        return in_array($this->type, ['select', 'radio', 'checkbox']);
    }

    /**
     * Check if this field requires data population.
     */
    public function isModelReference(): bool
    {
        return $this->type === 'model_reference';
    }

    /**
     * Check if this is a calculated field.
     */
    public function isCalculated(): bool
    {
        return $this->type === 'calculated';
    }
}
