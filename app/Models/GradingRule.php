<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GradingRule extends Model
{
    use LogsActivity;
    protected $fillable = [
        'form_field_id',
        'gender',
        'age_min',
        'age_max',
        'score_min',
        'score_max',
        'category',
        'order',
    ];

    protected $casts = [
        'age_min' => 'integer',
        'age_max' => 'integer',
        'score_min' => 'decimal:2',
        'score_max' => 'decimal:2',
    ];

    /**
     * Get the field this rule belongs to.
     */
    public function field(): BelongsTo
    {
        return $this->belongsTo(FormField::class, 'form_field_id');
    }

    /**
     * Check if this rule matches the given conditions.
     */
    public function matches(float $score, ?string $gender = null, ?int $age = null): bool
    {
        // Check score range
        if ($score < $this->score_min || $score > $this->score_max) {
            return false;
        }

        // Check gender if specified
        if ($this->gender !== 'all' && $gender !== null && $this->gender !== $gender) {
            return false;
        }

        // Check age range if specified
        if ($age !== null) {
            if ($this->age_min !== null && $age < $this->age_min) {
                return false;
            }
            if ($this->age_max !== null && $age > $this->age_max) {
                return false;
            }
        }

        return true;
    }
}
