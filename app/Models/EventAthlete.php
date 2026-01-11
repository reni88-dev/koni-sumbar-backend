<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventAthlete extends Model
{
    protected $fillable = [
        'event_id',
        'athlete_id',
        'cabor_id',
        'competition_class_id',
        'status',
        'notes',
    ];

    protected $casts = [
        'event_id' => 'integer',
        'athlete_id' => 'integer',
        'cabor_id' => 'integer',
        'competition_class_id' => 'integer',
    ];

    /**
     * Get the event.
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the athlete.
     */
    public function athlete(): BelongsTo
    {
        return $this->belongsTo(Athlete::class);
    }

    /**
     * Get the cabor.
     */
    public function cabor(): BelongsTo
    {
        return $this->belongsTo(Cabor::class);
    }

    /**
     * Get the competition class.
     */
    public function competitionClass(): BelongsTo
    {
        return $this->belongsTo(CompetitionClass::class);
    }

    /**
     * Get status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'registered' => 'Terdaftar',
            'verified' => 'Terverifikasi',
            'rejected' => 'Ditolak',
            default => $this->status,
        };
    }

    /**
     * Get status color.
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'registered' => 'yellow',
            'verified' => 'green',
            'rejected' => 'red',
            default => 'gray',
        };
    }
}
