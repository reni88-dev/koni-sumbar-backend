<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormSubmissionValue extends Model
{
    use LogsActivity;
    protected $fillable = [
        'form_submission_id',
        'form_field_id',
        'value',
        'calculated_category',
        'file_info',
    ];

    protected $casts = [
        'file_info' => 'array',
    ];

    /**
     * Get the submission this value belongs to.
     */
    public function submission(): BelongsTo
    {
        return $this->belongsTo(FormSubmission::class, 'form_submission_id');
    }

    /**
     * Get the field this value is for.
     */
    public function field(): BelongsTo
    {
        return $this->belongsTo(FormField::class, 'form_field_id');
    }
}
