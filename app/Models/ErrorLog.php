<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class ErrorLog extends Model
{
    /**
     * Disable default timestamps, we only have created_at
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'user_name',
        'type',
        'severity',
        'title',
        'message',
        'exception_class',
        'exception_message',
        'file',
        'line',
        'trace',
        'url',
        'method',
        'request_data',
        'ip_address',
        'user_agent',
        'is_resolved',
        'resolution_notes',
        'resolved_by',
        'resolved_at',
        'created_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'trace' => 'array',
        'request_data' => 'array',
        'is_resolved' => 'boolean',
        'created_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    /**
     * Error type labels (human-readable)
     */
    public const TYPE_LABELS = [
        'validation' => 'Validasi Data',
        'auth' => 'Autentikasi',
        'authorization' => 'Otorisasi',
        'database' => 'Database',
        'server' => 'Server',
        'network' => 'Jaringan',
        'file' => 'File/Upload',
        'api' => 'API External',
        'unknown' => 'Tidak Diketahui',
    ];

    /**
     * Severity colors for UI
     */
    public const SEVERITY_COLORS = [
        'info' => 'blue',
        'warning' => 'yellow',
        'error' => 'red',
        'critical' => 'purple',
    ];

    /**
     * Severity labels
     */
    public const SEVERITY_LABELS = [
        'info' => 'Info',
        'warning' => 'Peringatan',
        'error' => 'Error',
        'critical' => 'Kritis',
    ];

    /**
     * Get the user who triggered the error.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who resolved the error.
     */
    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    /**
     * Get human-readable type label.
     */
    public function getTypeLabelAttribute(): string
    {
        return self::TYPE_LABELS[$this->type] ?? $this->type;
    }

    /**
     * Get severity color for UI.
     */
    public function getSeverityColorAttribute(): string
    {
        return self::SEVERITY_COLORS[$this->severity] ?? 'gray';
    }

    /**
     * Get severity label.
     */
    public function getSeverityLabelAttribute(): string
    {
        return self::SEVERITY_LABELS[$this->severity] ?? $this->severity;
    }

    /**
     * Scope to filter by type.
     */
    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to filter by severity.
     */
    public function scopeBySeverity(Builder $query, string $severity): Builder
    {
        return $query->where('severity', $severity);
    }

    /**
     * Scope for unresolved errors.
     */
    public function scopeUnresolved(Builder $query): Builder
    {
        return $query->where('is_resolved', false);
    }

    /**
     * Scope for resolved errors.
     */
    public function scopeResolved(Builder $query): Builder
    {
        return $query->where('is_resolved', true);
    }

    /**
     * Scope for today's errors.
     */
    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Scope to filter by date range.
     */
    public function scopeByDateRange(Builder $query, ?string $from, ?string $to): Builder
    {
        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }
        return $query;
    }

    /**
     * Scope to search in title or message.
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('message', 'like', "%{$search}%")
              ->orWhere('user_name', 'like', "%{$search}%");
        });
    }

    /**
     * Mark error as resolved.
     */
    public function markResolved(?string $notes = null): void
    {
        $this->update([
            'is_resolved' => true,
            'resolution_notes' => $notes,
            'resolved_by' => auth()->id(),
            'resolved_at' => now(),
        ]);
    }

    /**
     * Get distinct types for filter dropdown.
     */
    public static function getDistinctTypes(): array
    {
        return self::distinct()->pluck('type')->sort()->values()->toArray();
    }
}
