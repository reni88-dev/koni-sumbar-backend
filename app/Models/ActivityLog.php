<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class ActivityLog extends Model
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
        'action',
        'model_type',
        'model_name',
        'model_id',
        'record_name',
        'old_values',
        'new_values',
        'changed_fields',
        'ip_address',
        'user_agent',
        'url',
        'created_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'changed_fields' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Action colors for UI
     */
    public const ACTION_COLORS = [
        'created' => 'green',
        'updated' => 'blue',
        'deleted' => 'red',
        'restored' => 'purple',
    ];

    /**
     * Action labels for UI
     */
    public const ACTION_LABELS = [
        'created' => 'Membuat',
        'updated' => 'Memperbarui',
        'deleted' => 'Menghapus',
        'restored' => 'Memulihkan',
    ];

    /**
     * Get the user who performed the action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get human-readable action label.
     */
    public function getActionLabelAttribute(): string
    {
        return self::ACTION_LABELS[$this->action] ?? $this->action;
    }

    /**
     * Get action color for UI.
     */
    public function getActionColorAttribute(): string
    {
        return self::ACTION_COLORS[$this->action] ?? 'gray';
    }

    /**
     * Get a summary of changes for display.
     */
    public function getChangeSummaryAttribute(): string
    {
        if ($this->action === 'created') {
            return 'Record baru dibuat';
        }

        if ($this->action === 'deleted') {
            return 'Record dihapus';
        }

        if ($this->action === 'restored') {
            return 'Record dipulihkan';
        }

        $changedFields = $this->changed_fields ?? [];
        $count = count($changedFields);

        if ($count === 0) {
            return 'Tidak ada perubahan data';
        }

        if ($count <= 3) {
            return 'Mengubah: ' . implode(', ', $changedFields);
        }

        return "Mengubah {$count} field";
    }

    /**
     * Scope to filter by user.
     */
    public function scopeByUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to filter by model type.
     */
    public function scopeByModel(Builder $query, string $modelName): Builder
    {
        return $query->where('model_name', $modelName);
    }

    /**
     * Scope to filter by action.
     */
    public function scopeByAction(Builder $query, string $action): Builder
    {
        return $query->where('action', $action);
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
     * Scope for today's logs.
     */
    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Scope to search in record name or changed fields.
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('record_name', 'like', "%{$search}%")
              ->orWhere('user_name', 'like', "%{$search}%");
        });
    }

    /**
     * Get distinct model names for filter dropdown.
     */
    public static function getDistinctModels(): array
    {
        return self::distinct()->pluck('model_name')->sort()->values()->toArray();
    }
}
