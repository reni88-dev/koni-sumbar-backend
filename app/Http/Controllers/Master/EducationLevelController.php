<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\EducationLevel;
use Illuminate\Http\Request;

class EducationLevelController extends Controller
{
    /**
     * Display a listing of education levels.
     */
    public function index(Request $request)
    {
        $this->authorize('education_levels.view');
        
        $query = EducationLevel::query();

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        // Pagination
        $perPage = $request->get('per_page', 10);
        $educationLevels = $query->ordered()->paginate($perPage);

        return response()->json($educationLevels);
    }

    /**
     * Store a newly created education level.
     */
    public function store(Request $request)
    {
        $this->authorize('education_levels.create');
        
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:education_levels,code',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ], [
            'name.required' => 'Nama jenjang wajib diisi',
            'code.required' => 'Kode jenjang wajib diisi',
            'code.unique' => 'Kode jenjang sudah digunakan',
        ]);

        $educationLevel = EducationLevel::create([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'order' => $request->order ?? 0,
            'is_active' => $request->is_active ?? true,
        ]);

        return response()->json([
            'message' => 'Jenjang pendidikan berhasil ditambahkan',
            'education_level' => $educationLevel,
        ], 201);
    }

    /**
     * Display the specified education level.
     */
    public function show(EducationLevel $educationLevel)
    {
        return response()->json($educationLevel);
    }

    /**
     * Update the specified education level.
     */
    public function update(Request $request, EducationLevel $educationLevel)
    {
        $this->authorize('education_levels.edit');
        
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:education_levels,code,' . $educationLevel->id,
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $educationLevel->update([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'order' => $request->order ?? $educationLevel->order,
            'is_active' => $request->is_active ?? $educationLevel->is_active,
        ]);

        return response()->json([
            'message' => 'Jenjang pendidikan berhasil diupdate',
            'education_level' => $educationLevel,
        ]);
    }

    /**
     * Remove the specified education level.
     */
    public function destroy(EducationLevel $educationLevel)
    {
        $this->authorize('education_levels.delete');
        
        $educationLevel->delete();

        return response()->json([
            'message' => 'Jenjang pendidikan berhasil dihapus',
        ]);
    }

    /**
     * Get all education levels for dropdown.
     */
    public function all()
    {
        $educationLevels = EducationLevel::active()
            ->ordered()
            ->get(['id', 'name', 'code']);

        return response()->json($educationLevels);
    }
}
