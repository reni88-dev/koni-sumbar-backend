<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Event extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'year',
        'location',
        'start_date',
        'end_date',
        'description',
        'logo',
        'registration_start',
        'registration_end',
        'is_active',
    ];

    protected $casts = [
        'year' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'registration_start' => 'date',
        'registration_end' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Computed attributes to append to JSON.
     */
    protected $appends = ['is_registration_open', 'registration_status'];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($event) {
            if (empty($event->slug)) {
                $event->slug = Str::slug($event->name . '-' . $event->year);
            }
        });
    }

    /**
     * Get athletes participating in this event.
     */
    public function athletes(): BelongsToMany
    {
        return $this->belongsToMany(Athlete::class, 'event_athletes')
            ->withPivot(['cabor_id', 'status', 'notes'])
            ->withTimestamps();
    }

    /**
     * Get event athletes with cabor.
     */
    public function eventAthletes()
    {
        return $this->hasMany(EventAthlete::class);
    }

    /**
     * Get type label.
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'provincial' => 'Provinsi',
            'national' => 'Nasional',
            'international' => 'Internasional',
            default => $this->type,
        };
    }

    /**
     * Check if registration is open.
     */
    public function getIsRegistrationOpenAttribute(): bool
    {
        if (!$this->registration_start || !$this->registration_end) {
            return true; // If no registration dates set, assume always open
        }
        
        $now = now()->startOfDay();
        return $now->gte($this->registration_start) && $now->lte($this->registration_end);
    }

    /**
     * Get registration status text.
     */
    public function getRegistrationStatusAttribute(): string
    {
        if (!$this->registration_start || !$this->registration_end) {
            return 'Dibuka';
        }
        
        $now = now()->startOfDay();
        
        if ($now->lt($this->registration_start)) {
            return 'Belum Dibuka';
        }
        
        if ($now->gt($this->registration_end)) {
            return 'Ditutup';
        }
        
        return 'Dibuka';
    }

    /**
     * Scope for active events.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for upcoming events.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>=', now());
    }
}
