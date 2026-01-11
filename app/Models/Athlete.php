<?php

namespace App\Models;

use App\Casts\Encrypted;
use App\Traits\HasBlindIndex;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Athlete extends Model
{
    use SoftDeletes, HasBlindIndex;

    /**
     * Fields that need blind index hashing.
     * Format: ['plain_field' => 'hash_field']
     */
    protected array $blindIndexFields = [
        'nik' => 'nik_hash',
    ];

    protected $fillable = [
        'cabor_id',
        'education_level_id',
        'name',
        'nik',
        'nik_hash',
        'no_kk',
        'competition_class',
        'birth_place',
        'birth_date',
        'gender',
        'religion',
        'address',
        'education',
        'blood_type',
        'occupation',
        'marital_status',
        'hobby',
        'height',
        'weight',
        'phone',
        'email',
        'career_start_year',
        'injury_illness_history',
        'top_achievements',
        'provincial_achievements',
        'national_achievements',
        'international_achievements',
        'photo',
        'is_active',
    ];

    /**
     * Hidden from JSON output (don't expose hash).
     */
    protected $hidden = ['nik_hash'];

    protected $casts = [
        'nik' => Encrypted::class,
        'no_kk' => Encrypted::class,
        'birth_date' => 'date',
        'career_start_year' => 'integer',
        'height' => 'integer',
        'weight' => 'decimal:2',
        'is_active' => 'boolean',
        'top_achievements' => 'array',
        'provincial_achievements' => 'array',
        'national_achievements' => 'array',
        'international_achievements' => 'array',
    ];

    /**
     * Get the cabor (sport branch) of this athlete.
     */
    public function cabor(): BelongsTo
    {
        return $this->belongsTo(Cabor::class);
    }

    /**
     * Get the education level.
     */
    public function educationLevel(): BelongsTo
    {
        return $this->belongsTo(EducationLevel::class);
    }

    /**
     * Get events this athlete participates in.
     */
    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'event_athletes')
            ->withPivot(['cabor_id', 'status', 'notes'])
            ->withTimestamps();
    }

    /**
     * Get athlete's age.
     */
    public function getAgeAttribute(): ?int
    {
        return $this->birth_date?->age;
    }

    /**
     * Get athlete's full birth info.
     */
    public function getBirthInfoAttribute(): ?string
    {
        if (!$this->birth_place && !$this->birth_date) {
            return null;
        }
        
        $place = $this->birth_place ?? '';
        $date = $this->birth_date?->format('d F Y') ?? '';
        
        return trim("{$place}, {$date}", ', ');
    }

    /**
     * Get years of career experience.
     */
    public function getCareerYearsAttribute(): ?int
    {
        if (!$this->career_start_year) {
            return null;
        }
        
        return now()->year - $this->career_start_year;
    }

    /**
     * Scope for active athletes.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for athletes by cabor.
     */
    public function scopeByCabor($query, $caborId)
    {
        return $query->where('cabor_id', $caborId);
    }
}

