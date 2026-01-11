<?php

namespace App\Rules;

use App\Models\Athlete;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Validates NIK uniqueness using blind index (hash).
 * 
 * This is O(1) database lookup instead of O(n) decryption loop.
 * Uses the nik_hash column for fast unique checking.
 */
class UniqueNikHash implements ValidationRule
{
    /**
     * The ID to ignore (for update scenarios).
     */
    protected ?int $ignoreId;

    /**
     * Create a new rule instance.
     *
     * @param int|null $ignoreId The athlete ID to ignore (for updates)
     */
    public function __construct(?int $ignoreId = null)
    {
        $this->ignoreId = $ignoreId;
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            return; // Allow null/empty values
        }

        // Generate hash for the input value
        $hash = Athlete::generateBlindIndex($value);

        // Check if hash already exists
        $query = Athlete::where('nik_hash', $hash);

        // Exclude current athlete when updating
        if ($this->ignoreId) {
            $query->where('id', '!=', $this->ignoreId);
        }

        if ($query->exists()) {
            $fail('NIK sudah terdaftar.');
        }
    }
}
