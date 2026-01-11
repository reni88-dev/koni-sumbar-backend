<?php

namespace App\Traits;

use Illuminate\Support\Facades\Hash;

/**
 * Trait for generating blind index hashes for encrypted fields.
 * 
 * Blind indexes allow searching/unique checking on encrypted data
 * without decrypting all records. Uses a deterministic hash (same input = same output).
 */
trait HasBlindIndex
{
    /**
     * Generate a blind index hash for a given value.
     * Uses HMAC-SHA256 with app key for security.
     *
     * @param string|null $value The plain text value to hash
     * @return string|null The hash, or null if value is null/empty
     */
    public static function generateBlindIndex(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        // Use HMAC with app key for deterministic but secure hashing
        return hash_hmac('sha256', $value, config('app.key'));
    }

    /**
     * Boot the trait - auto-generate hash on saving.
     */
    protected static function bootHasBlindIndex(): void
    {
        static::saving(function ($model) {
            // Get fields that need blind indexing from model property
            $blindIndexFields = $model->blindIndexFields ?? [];

            foreach ($blindIndexFields as $field => $hashField) {
                $originalValue = $model->getOriginal($field);
                $newValue = $model->getAttribute($field);

                // Only regenerate hash if field has changed or hash is missing
                if ($originalValue !== $newValue || empty($model->getAttribute($hashField))) {
                    $model->setAttribute($hashField, static::generateBlindIndex($newValue));
                }
            }
        });
    }
}
