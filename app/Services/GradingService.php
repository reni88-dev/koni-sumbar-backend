<?php

namespace App\Services;

use App\Models\FormField;
use App\Models\GradingRule;

class GradingService
{
    /**
     * Get the category for a score based on grading rules.
     */
    public function getCategory(FormField $field, float $score, ?string $gender = null, ?int $age = null): ?string
    {
        if (!$field->has_grading) {
            return null;
        }

        $rules = $field->gradingRules()->orderBy('order')->get();

        foreach ($rules as $rule) {
            if ($rule->matches($score, $gender, $age)) {
                return $rule->category;
            }
        }

        return null;
    }

    /**
     * Process grading for a submission value.
     */
    public function processGrading(array $fieldId, $value, ?string $gender = null, ?int $age = null): ?string
    {
        $field = FormField::find($fieldId);
        
        if (!$field || !is_numeric($value)) {
            return null;
        }

        return $this->getCategory($field, (float) $value, $gender, $age);
    }

    /**
     * Create default grading rules for a field.
     */
    public function createDefaultRules(FormField $field, array $rules): void
    {
        foreach ($rules as $index => $rule) {
            GradingRule::create([
                'form_field_id' => $field->id,
                'gender' => $rule['gender'] ?? 'all',
                'age_min' => $rule['age_min'] ?? null,
                'age_max' => $rule['age_max'] ?? null,
                'score_min' => $rule['score_min'],
                'score_max' => $rule['score_max'],
                'category' => $rule['category'],
                'order' => $index,
            ]);
        }
    }
}
