<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class FormSubmission extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'form_template_id',
        'reference_id',
        'user_id',
        'submission_code',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($submission) {
            if (empty($submission->submission_code)) {
                $submission->submission_code = 'SUB-' . strtoupper(Str::random(8));
            }
        });
    }

    /**
     * Get the form template.
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(FormTemplate::class, 'form_template_id');
    }

    /**
     * Get the user who submitted this form.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all values in this submission.
     */
    public function values(): HasMany
    {
        return $this->hasMany(FormSubmissionValue::class);
    }

    /**
     * Get the referenced model record (e.g., Athlete).
     */
    public function getReferenceRecord()
    {
        if (!$this->reference_id || !$this->template) {
            return null;
        }

        $modelClass = $this->template->getReferenceModelClass();
        
        if (!$modelClass) {
            return null;
        }

        return $modelClass::find($this->reference_id);
    }

    /**
     * Get value for a specific field.
     */
    public function getValueForField(int $fieldId): ?FormSubmissionValue
    {
        return $this->values->firstWhere('form_field_id', $fieldId);
    }
}
