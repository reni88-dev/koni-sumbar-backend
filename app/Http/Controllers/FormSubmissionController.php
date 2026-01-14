<?php

namespace App\Http\Controllers;

use App\Models\FormSubmission;
use App\Models\FormSubmissionValue;
use App\Models\FormTemplate;
use App\Services\GradingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FormSubmissionController extends Controller
{
    protected GradingService $gradingService;

    public function __construct(GradingService $gradingService)
    {
        $this->gradingService = $gradingService;
    }

    /**
     * List submissions for a form template.
     */
    public function index(Request $request, FormTemplate $formTemplate)
    {
        $query = $formTemplate->submissions()
            ->with(['user', 'values.field', 'event']);

        // Filter by event
        if ($request->has('event_id') && $request->event_id) {
            $query->where('event_id', $request->event_id);
        }

        // Search by submission code
        if ($request->has('search') && $request->search) {
            $query->where('submission_code', 'like', "%{$request->search}%");
        }

        $submissions = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 10));

        // Add reference record info
        foreach ($submissions as $submission) {
            $submission->reference_record = $submission->getReferenceRecord();
        }

        return response()->json($submissions);
    }

    /**
     * Get a single submission.
     */
    public function show(FormSubmission $formSubmission)
    {
        $formSubmission->load([
            'template.sections.fields',
            'user',
            'values.field',
        ]);

        $formSubmission->reference_record = $formSubmission->getReferenceRecord();

        return response()->json($formSubmission);
    }

    /**
     * Submit a form.
     */
    public function store(Request $request, FormTemplate $formTemplate)
    {
        $validated = $request->validate([
            'reference_id' => 'nullable|integer',
            'event_id' => 'nullable|integer|exists:events,id',
            'values' => 'required|array',
            'values.*.field_id' => 'required|integer|exists:form_fields,id',
            'values.*.value' => 'nullable',
        ]);

        // Get reference record for grading context (gender, age)
        $referenceRecord = null;
        $gender = null;
        $age = null;

        if (!empty($validated['reference_id']) && $formTemplate->reference_model) {
            $modelClass = $formTemplate->getReferenceModelClass();
            if ($modelClass) {
                $referenceRecord = $modelClass::find($validated['reference_id']);
                if ($referenceRecord) {
                    $gender = $referenceRecord->gender ?? null;
                    $age = $referenceRecord->age ?? null;
                }
            }
        }

        DB::beginTransaction();

        try {
            // Create submission
            $submission = FormSubmission::create([
                'form_template_id' => $formTemplate->id,
                'event_id' => $validated['event_id'] ?? null,
                'reference_id' => $validated['reference_id'] ?? null,
                'user_id' => auth()->id(),
            ]);

            // Create values
            foreach ($validated['values'] as $valueData) {
                $field = \App\Models\FormField::find($valueData['field_id']);
                $value = $valueData['value'];

                // Calculate category if field has grading
                $calculatedCategory = null;
                if ($field && $field->has_grading && is_numeric($value)) {
                    $calculatedCategory = $this->gradingService->getCategory(
                        $field,
                        (float) $value,
                        $gender,
                        $age
                    );
                }

                FormSubmissionValue::create([
                    'form_submission_id' => $submission->id,
                    'form_field_id' => $valueData['field_id'],
                    'value' => is_array($value) ? json_encode($value) : $value,
                    'calculated_category' => $calculatedCategory,
                ]);
            }

            DB::commit();

            $submission->load(['values.field', 'user']);
            $submission->reference_record = $submission->getReferenceRecord();

            return response()->json([
                'message' => 'Form berhasil disubmit',
                'submission' => $submission,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal submit form',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update a submission.
     */
    public function update(Request $request, FormSubmission $formSubmission)
    {
        $validated = $request->validate([
            'values' => 'required|array',
            'values.*.field_id' => 'required|integer|exists:form_fields,id',
            'values.*.value' => 'nullable',
        ]);

        // Get reference context
        $referenceRecord = $formSubmission->getReferenceRecord();
        $gender = $referenceRecord?->gender;
        $age = $referenceRecord?->age;

        DB::beginTransaction();

        try {
            // Delete old values
            $formSubmission->values()->delete();

            // Create new values
            foreach ($validated['values'] as $valueData) {
                $field = \App\Models\FormField::find($valueData['field_id']);
                $value = $valueData['value'];

                $calculatedCategory = null;
                if ($field && $field->has_grading && is_numeric($value)) {
                    $calculatedCategory = $this->gradingService->getCategory(
                        $field,
                        (float) $value,
                        $gender,
                        $age
                    );
                }

                FormSubmissionValue::create([
                    'form_submission_id' => $formSubmission->id,
                    'form_field_id' => $valueData['field_id'],
                    'value' => is_array($value) ? json_encode($value) : $value,
                    'calculated_category' => $calculatedCategory,
                ]);
            }

            DB::commit();

            $formSubmission->load(['values.field', 'user']);

            return response()->json([
                'message' => 'Submission berhasil diupdate',
                'submission' => $formSubmission,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal update submission',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a submission.
     */
    public function destroy(FormSubmission $formSubmission)
    {
        $formSubmission->delete();

        return response()->json([
            'message' => 'Submission berhasil dihapus',
        ]);
    }

    /**
     * Get grading preview for a value.
     */
    public function previewGrading(Request $request)
    {
        $validated = $request->validate([
            'field_id' => 'required|integer|exists:form_fields,id',
            'score' => 'required|numeric',
            'gender' => 'nullable|string',
            'age' => 'nullable|integer',
        ]);

        $field = \App\Models\FormField::find($validated['field_id']);
        
        if (!$field->has_grading) {
            return response()->json(['category' => null]);
        }

        $category = $this->gradingService->getCategory(
            $field,
            (float) $validated['score'],
            $validated['gender'] ?? null,
            $validated['age'] ?? null
        );

        return response()->json(['category' => $category]);
    }
}
