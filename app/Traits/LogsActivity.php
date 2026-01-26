<?php

namespace App\Traits;

use App\Services\ActivityLogService;

trait LogsActivity
{
    /**
     * Boot the trait and register model event listeners.
     */
    protected static function bootLogsActivity(): void
    {
        // Log when a new record is created
        static::created(function ($model) {
            ActivityLogService::log('created', $model);
        });

        // Capture old values before update
        static::updating(function ($model) {
            $model->_oldValuesForActivityLog = $model->getOriginal();
        });

        // Log after update is complete
        static::updated(function ($model) {
            $oldValues = $model->_oldValuesForActivityLog ?? null;
            unset($model->_oldValuesForActivityLog);
            ActivityLogService::log('updated', $model, $oldValues);
        });

        // Log when a record is deleted
        static::deleted(function ($model) {
            ActivityLogService::log('deleted', $model);
        });

        // Log when a soft-deleted record is restored
        if (method_exists(static::class, 'restored')) {
            static::restored(function ($model) {
                ActivityLogService::log('restored', $model);
            });
        }
    }

    /**
     * Get the name to display in activity log.
     * Override this in your model for custom display names.
     */
    public function getActivityLogName(): ?string
    {
        // Try common name fields
        foreach (['name', 'title', 'label', 'email'] as $field) {
            if (isset($this->{$field})) {
                return $this->{$field};
            }
        }

        return null;
    }

    /**
     * Get fields that should be excluded from activity log.
     * Override this in your model to add custom exclusions.
     */
    public function getActivityLogExcludedFields(): array
    {
        return [];
    }
}
