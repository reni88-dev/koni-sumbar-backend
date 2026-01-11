<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidKtp implements ValidationRule
{
    /**
     * Validate Indonesian KTP number format.
     * Format: 16 digits with valid province, regency, district codes.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Remove any spaces or dashes
        $ktp = preg_replace('/[\s\-]/', '', $value);

        // Must be exactly 16 digits
        if (!preg_match('/^\d{16}$/', $ktp)) {
            $fail('The :attribute must be exactly 16 digits.');
            return;
        }

        // Extract components
        $provinceCode = substr($ktp, 0, 2);
        $regencyCode = substr($ktp, 2, 2);
        $districtCode = substr($ktp, 4, 2);
        $birthDate = substr($ktp, 6, 6);
        $sequence = substr($ktp, 12, 4);

        // Validate province code (01-99, excluding invalid)
        $invalidProvinceCodes = ['00'];
        if (in_array($provinceCode, $invalidProvinceCodes)) {
            $fail('The :attribute has an invalid province code.');
            return;
        }

        // Extract birth date components
        $day = (int) substr($birthDate, 0, 2);
        $month = (int) substr($birthDate, 2, 2);
        $year = (int) substr($birthDate, 4, 2);

        // For females, day is +40
        if ($day > 40) {
            $day -= 40;
        }

        // Validate day (1-31)
        if ($day < 1 || $day > 31) {
            $fail('The :attribute has an invalid birth date.');
            return;
        }

        // Validate month (1-12)
        if ($month < 1 || $month > 12) {
            $fail('The :attribute has an invalid birth month.');
            return;
        }

        // Sequence number validation (0001-9999)
        if ((int) $sequence < 1) {
            $fail('The :attribute has an invalid sequence number.');
            return;
        }
    }
}
