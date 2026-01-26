<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogService
{
    /**
     * Log an activity for a model.
     */
    public static function log(string $action, Model $model, ?array $oldValues = null): void
    {
        // Don't log ActivityLog itself to prevent infinite loop
        if ($model instanceof ActivityLog) {
            return;
        }

        $user = Auth::user();
        $newValues = $action !== 'deleted' ? $model->getAttributes() : null;
        
        // For updates, calculate what actually changed
        $changedFields = null;
        if ($action === 'updated' && $oldValues !== null) {
            $changedFields = self::getChangedFields($oldValues, $newValues ?? [], $model);
            
            // If nothing actually changed, don't log
            if (empty($changedFields)) {
                return;
            }
        }

        // Filter out sensitive and excluded fields
        $excludedFields = self::getExcludedFields($model);
        $oldValues = self::filterSensitiveData($oldValues, $excludedFields);
        $newValues = self::filterSensitiveData($newValues, $excludedFields);

        ActivityLog::create([
            'user_id' => $user?->id,
            'user_name' => $user?->name ?? 'System',
            'action' => $action,
            'model_type' => get_class($model),
            'model_name' => class_basename($model),
            'model_id' => $model->getKey(),
            'record_name' => self::getRecordName($model),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'changed_fields' => $changedFields,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'url' => Request::fullUrl(),
            'created_at' => now(),
        ]);
    }

    /**
     * Get the display name for a record.
     */
    protected static function getRecordName(Model $model): ?string
    {
        // Check if model has custom method
        if (method_exists($model, 'getActivityLogName')) {
            return $model->getActivityLogName();
        }

        // Try common name fields
        foreach (['name', 'title', 'label', 'email', 'display_name'] as $field) {
            if (isset($model->{$field})) {
                return $model->{$field};
            }
        }

        return null;
    }

    /**
     * Get fields that should be excluded from logging.
     */
    protected static function getExcludedFields(Model $model): array
    {
        $defaults = [
            'password',
            'remember_token',
            'two_factor_secret',
            'two_factor_recovery_codes',
            'updated_at',
        ];

        // Check if model has custom excluded fields
        if (method_exists($model, 'getActivityLogExcludedFields')) {
            return array_merge($defaults, $model->getActivityLogExcludedFields());
        }

        return $defaults;
    }

    /**
     * Calculate which fields actually changed.
     */
    protected static function getChangedFields(?array $old, array $new, Model $model): array
    {
        if ($old === null) {
            return [];
        }

        $changed = [];
        $excluded = self::getExcludedFields($model);

        foreach ($new as $key => $value) {
            if (in_array($key, $excluded)) {
                continue;
            }

            // Check if value actually changed
            $oldValue = $old[$key] ?? null;
            if ($oldValue !== $value) {
                $changed[] = $key;
            }
        }

        return $changed;
    }

    /**
     * Filter out sensitive data from values.
     */
    protected static function filterSensitiveData(?array $data, array $excludedFields): ?array
    {
        if ($data === null) {
            return null;
        }

        return array_diff_key($data, array_flip($excludedFields));
    }
}
