<?php

namespace App\Http\Controllers;

use App\Models\FormField;
use App\Models\FormFieldOption;
use App\Models\FormSection;
use App\Models\FormTemplate;
use App\Models\GradingRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FormBuilderController extends Controller
{
    /**
     * List all form templates.
     */
    public function index(Request $request)
    {
        $query = FormTemplate::with(['creator', 'sections'])
            ->withCount('submissions');

        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $templates = $query->orderBy('created_at', 'desc')->paginate($request->get('per_page', 10));

        return response()->json($templates);
    }

    /**
     * Get a single form template with all details.
     */
    public function show(FormTemplate $formTemplate)
    {
        $formTemplate->load([
            'sections.fields.options',
            'sections.fields.gradingRules',
            'creator',
        ]);

        // Add options data for select fields
        foreach ($formTemplate->sections as $section) {
            foreach ($section->fields as $field) {
                if ($field->isSelectable()) {
                    $field->options_data = $field->getOptionsData();
                }
            }
        }

        return response()->json($formTemplate);
    }

    /**
     * Store a new form template.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'reference_model' => 'nullable|string',
            'reference_display_field' => 'nullable|string',
            'is_active' => 'boolean',
            'sections' => 'required|array|min:1',
            'sections.*.title' => 'required|string|max:255',
            'sections.*.type' => 'required|in:normal,table',
            'sections.*.table_columns' => 'nullable|array',
            'sections.*.fields' => 'nullable|array',
            'sections.*.fields.*.label' => 'required|string|max:255',
            'sections.*.fields.*.name' => 'required|string|max:255',
            'sections.*.fields.*.type' => 'required|string',
        ]);

        DB::beginTransaction();

        try {
            // Generate unique slug
            $baseSlug = Str::slug($validated['name']);
            $slug = $baseSlug;
            $counter = 1;
            while (FormTemplate::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }

            // Create form template
            $template = FormTemplate::create([
                'name' => $validated['name'],
                'slug' => $slug,
                'description' => $validated['description'] ?? null,
                'reference_model' => $validated['reference_model'] ?? null,
                'reference_display_field' => $validated['reference_display_field'] ?? 'name',
                'is_active' => $validated['is_active'] ?? true,
                'created_by' => auth()->id(),
            ]);

            // Create sections and fields
            foreach ($validated['sections'] as $sectionIndex => $sectionData) {
                $section = $template->sections()->create([
                    'title' => $sectionData['title'],
                    'type' => $sectionData['type'],
                    'order' => $sectionIndex,
                    'table_columns' => $sectionData['table_columns'] ?? null,
                ]);

                // Only create fields if they exist
                $fields = $sectionData['fields'] ?? [];
                foreach ($fields as $fieldIndex => $fieldData) {
                    $field = $section->fields()->create([
                        'label' => $fieldData['label'],
                        'name' => $fieldData['name'],
                        'placeholder' => $fieldData['placeholder'] ?? null,
                        'type' => $fieldData['type'],
                        'group_label' => $fieldData['group_label'] ?? null,
                        'sub_label' => $fieldData['sub_label'] ?? null,
                        'technique' => $fieldData['technique'] ?? null,
                        'unit' => $fieldData['unit'] ?? null,
                        'is_required' => $fieldData['is_required'] ?? false,
                        'validation_rules' => $fieldData['validation_rules'] ?? null,
                        'order' => $fieldIndex,
                        'data_source_type' => $fieldData['data_source_type'] ?? null,
                        'data_source_model' => $fieldData['data_source_model'] ?? null,
                        'data_source_value_field' => $fieldData['data_source_value_field'] ?? null,
                        'data_source_label_field' => $fieldData['data_source_label_field'] ?? null,
                        'data_source_filters' => $fieldData['data_source_filters'] ?? null,
                        'reference_field' => $fieldData['reference_field'] ?? null,
                        'linked_to_reference_field' => $fieldData['linked_to_reference_field'] ?? null,
                        'is_readonly' => $fieldData['is_readonly'] ?? false,
                        'calculation_formula' => $fieldData['calculation_formula'] ?? null,
                        'calculation_dependencies' => $fieldData['calculation_dependencies'] ?? null,
                        'has_grading' => $fieldData['has_grading'] ?? false,
                    ]);

                    // Create custom options if provided
                    if (!empty($fieldData['options'])) {
                        foreach ($fieldData['options'] as $optionIndex => $option) {
                            $field->options()->create([
                                'label' => $option['label'],
                                'value' => $option['value'],
                                'order' => $optionIndex,
                            ]);
                        }
                    }

                    // Create grading rules if provided
                    if (!empty($fieldData['grading_rules'])) {
                        foreach ($fieldData['grading_rules'] as $ruleIndex => $rule) {
                            $field->gradingRules()->create([
                                'gender' => $rule['gender'] ?? 'all',
                                'age_min' => $rule['age_min'] ?? null,
                                'age_max' => $rule['age_max'] ?? null,
                                'score_min' => $rule['score_min'],
                                'score_max' => $rule['score_max'],
                                'category' => $rule['category'],
                                'order' => $ruleIndex,
                            ]);
                        }
                    }
                }
            }

            DB::commit();

            $template->load(['sections.fields.options', 'sections.fields.gradingRules']);

            return response()->json([
                'message' => 'Form template berhasil dibuat',
                'form_template' => $template,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Form template creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'message' => 'Gagal membuat form template',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update a form template.
     */
    public function update(Request $request, FormTemplate $formTemplate)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'reference_model' => 'nullable|string',
            'reference_display_field' => 'nullable|string',
            'is_active' => 'boolean',
            'sections' => 'required|array|min:1',
            'sections.*.id' => 'nullable|integer',
            'sections.*.title' => 'required|string|max:255',
            'sections.*.type' => 'required|in:normal,table',
            'sections.*.table_columns' => 'nullable|array',
            'sections.*.fields' => 'nullable|array',
        ]);

        DB::beginTransaction();

        try {
            // Update template
            $formTemplate->update([
                'name' => $validated['name'],
                'slug' => Str::slug($validated['name']),
                'description' => $validated['description'] ?? null,
                'reference_model' => $validated['reference_model'] ?? null,
                'reference_display_field' => $validated['reference_display_field'] ?? 'name',
                'is_active' => $validated['is_active'] ?? true,
            ]);

            // Delete old sections (cascade deletes fields)
            $formTemplate->sections()->delete();

            // Re-create sections and fields
            foreach ($validated['sections'] as $sectionIndex => $sectionData) {
                $section = $formTemplate->sections()->create([
                    'title' => $sectionData['title'],
                    'type' => $sectionData['type'],
                    'order' => $sectionIndex,
                    'table_columns' => $sectionData['table_columns'] ?? null,
                ]);

                foreach ($sectionData['fields'] as $fieldIndex => $fieldData) {
                    $field = $section->fields()->create([
                        'label' => $fieldData['label'],
                        'name' => $fieldData['name'],
                        'placeholder' => $fieldData['placeholder'] ?? null,
                        'type' => $fieldData['type'],
                        'group_label' => $fieldData['group_label'] ?? null,
                        'sub_label' => $fieldData['sub_label'] ?? null,
                        'technique' => $fieldData['technique'] ?? null,
                        'unit' => $fieldData['unit'] ?? null,
                        'is_required' => $fieldData['is_required'] ?? false,
                        'validation_rules' => $fieldData['validation_rules'] ?? null,
                        'order' => $fieldIndex,
                        'data_source_type' => $fieldData['data_source_type'] ?? null,
                        'data_source_model' => $fieldData['data_source_model'] ?? null,
                        'data_source_value_field' => $fieldData['data_source_value_field'] ?? null,
                        'data_source_label_field' => $fieldData['data_source_label_field'] ?? null,
                        'data_source_filters' => $fieldData['data_source_filters'] ?? null,
                        'reference_field' => $fieldData['reference_field'] ?? null,
                        'linked_to_reference_field' => $fieldData['linked_to_reference_field'] ?? null,
                        'is_readonly' => $fieldData['is_readonly'] ?? false,
                        'calculation_formula' => $fieldData['calculation_formula'] ?? null,
                        'calculation_dependencies' => $fieldData['calculation_dependencies'] ?? null,
                        'has_grading' => $fieldData['has_grading'] ?? false,
                    ]);

                    if (!empty($fieldData['options'])) {
                        foreach ($fieldData['options'] as $optionIndex => $option) {
                            $field->options()->create([
                                'label' => $option['label'],
                                'value' => $option['value'],
                                'order' => $optionIndex,
                            ]);
                        }
                    }

                    if (!empty($fieldData['grading_rules'])) {
                        foreach ($fieldData['grading_rules'] as $ruleIndex => $rule) {
                            $field->gradingRules()->create([
                                'gender' => $rule['gender'] ?? 'all',
                                'age_min' => $rule['age_min'] ?? null,
                                'age_max' => $rule['age_max'] ?? null,
                                'score_min' => $rule['score_min'],
                                'score_max' => $rule['score_max'],
                                'category' => $rule['category'],
                                'order' => $ruleIndex,
                            ]);
                        }
                    }
                }
            }

            DB::commit();

            $formTemplate->load(['sections.fields.options', 'sections.fields.gradingRules']);

            return response()->json([
                'message' => 'Form template berhasil diupdate',
                'form_template' => $formTemplate,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal update form template',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a form template.
     */
    public function destroy(FormTemplate $formTemplate)
    {
        $formTemplate->delete();

        return response()->json([
            'message' => 'Form template berhasil dihapus',
        ]);
    }

    /**
     * Get available models for data source.
     */
    public function getAvailableModels()
    {
        $models = [];
        
        foreach (FormTemplate::$allowedModels as $key => $class) {
            $models[] = [
                'key' => $key,
                'name' => class_basename($class),
            ];
        }

        return response()->json($models);
    }

    /**
     * Get fields from a model for value/label selection.
     */
    public function getModelFields(string $model)
    {
        $modelClass = FormTemplate::$allowedModels[$model] ?? null;

        if (!$modelClass || !class_exists($modelClass)) {
            return response()->json(['error' => 'Model not found'], 404);
        }

        $instance = new $modelClass();
        $fillable = $instance->getFillable();
        
        // Add common computed attributes
        $appends = $instance->getAppends ?? [];

        $fields = array_merge($fillable, $appends);

        return response()->json($fields);
    }

    /**
     * Get reference data for a specific record.
     */
    public function getReferenceData(FormTemplate $formTemplate, int $referenceId)
    {
        $modelClass = $formTemplate->getReferenceModelClass();

        if (!$modelClass) {
            return response()->json(['error' => 'No reference model configured'], 400);
        }

        $record = $modelClass::find($referenceId);

        if (!$record) {
            return response()->json(['error' => 'Record not found'], 404);
        }

        return response()->json($record);
    }

    /**
     * Get all records from a model for selection in model_reference fields.
     * Supports event_id filter for athlete model to only show event-registered athletes.
     */
    public function getModelRecords(Request $request, string $model)
    {
        $modelClass = FormTemplate::$allowedModels[$model] ?? null;

        if (!$modelClass || !class_exists($modelClass)) {
            return response()->json(['error' => 'Model not found'], 404);
        }

        $query = $modelClass::query();

        // Filter athletes by event if event_id is provided
        if ($model === 'athlete' && $request->has('event_id')) {
            $eventId = $request->get('event_id');
            $query->whereHas('events', function ($q) use ($eventId) {
                $q->where('events.id', $eventId);
            });
        }

        // Fetch records with commonly used display fields
        $records = $query->select('*')
            ->orderBy('name', 'asc')
            ->limit(500) // Limit to prevent too many records
            ->get();

        return response()->json($records);
    }
}
