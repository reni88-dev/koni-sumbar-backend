<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class FormTemplate extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'reference_model',
        'reference_display_field',
        'is_active',
        'settings',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'array',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($template) {
            if (empty($template->slug)) {
                $template->slug = Str::slug($template->name);
            }
        });
    }

    /**
     * Available models for reference/data source.
     */
    public static array $allowedModels = [
        'athlete' => \App\Models\Athlete::class,
        'cabor' => \App\Models\Cabor::class,
        'event' => \App\Models\Event::class,
        'education_level' => \App\Models\EducationLevel::class,
        'user' => \App\Models\User::class,
    ];

    /**
     * Get the creator of this form.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all sections in this form.
     */
    public function sections(): HasMany
    {
        return $this->hasMany(FormSection::class)->orderBy('order');
    }

    /**
     * Get all submissions for this form.
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(FormSubmission::class);
    }

    /**
     * Get all fields across all sections.
     */
    public function allFields()
    {
        return FormField::whereIn('form_section_id', $this->sections()->pluck('id'));
    }

    /**
     * Scope for active forms.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the reference model class.
     */
    public function getReferenceModelClass(): ?string
    {
        if (!$this->reference_model) {
            return null;
        }

        // Check if it's a full class name or a key
        if (class_exists($this->reference_model)) {
            return $this->reference_model;
        }

        return self::$allowedModels[$this->reference_model] ?? null;
    }
}
