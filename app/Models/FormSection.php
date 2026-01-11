<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormSection extends Model
{
    protected $fillable = [
        'form_template_id',
        'title',
        'type',
        'order',
        'table_columns',
        'settings',
    ];

    protected $casts = [
        'table_columns' => 'array',
        'settings' => 'array',
    ];

    /**
     * Get the form template.
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(FormTemplate::class, 'form_template_id');
    }

    /**
     * Get all fields in this section.
     */
    public function fields(): HasMany
    {
        return $this->hasMany(FormField::class)->orderBy('order');
    }

    /**
     * Check if this is a table section.
     */
    public function isTable(): bool
    {
        return $this->type === 'table';
    }
}
