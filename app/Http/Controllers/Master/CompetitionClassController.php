<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\CompetitionClass;
use Illuminate\Http\Request;

class CompetitionClassController extends Controller
{
    /**
     * Display a listing of competition classes.
     */
    public function index(Request $request)
    {
        $this->authorize('competition_classes.view');
        
        $query = CompetitionClass::with('cabor:id,name')
            ->withCount('athletes');

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhereHas('cabor', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by cabor
        if ($request->has('cabor_id') && $request->cabor_id) {
            $query->where('cabor_id', $request->cabor_id);
        }

        // Filter by status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // Pagination
        $perPage = $request->get('per_page', 20);
        $classes = $query->orderBy('cabor_id')->orderBy('name')->paginate($perPage);

        return response()->json($classes);
    }

    /**
     * Store a newly created competition class.
     */
    public function store(Request $request)
    {
        $this->authorize('competition_classes.create');
        
        $request->validate([
            'cabor_id' => 'required|exists:cabors,id',
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ], [
            'cabor_id.required' => 'Cabang olahraga wajib dipilih',
            'cabor_id.exists' => 'Cabang olahraga tidak valid',
            'name.required' => 'Nama kelas wajib diisi',
        ]);

        $competitionClass = CompetitionClass::create([
            'cabor_id' => $request->cabor_id,
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'is_active' => $request->is_active ?? true,
        ]);

        $competitionClass->load('cabor:id,name');

        return response()->json([
            'message' => 'Kelas pertandingan berhasil ditambahkan',
            'competition_class' => $competitionClass,
        ], 201);
    }

    /**
     * Store multiple competition classes in a single transaction.
     */
    public function storeBatch(Request $request)
    {
        $this->authorize('competition_classes.create');
        
        $request->validate([
            'cabor_id' => 'required|exists:cabors,id',
            'items' => 'required|array|min:1|max:50',
            'items.*.name' => 'required|string|max:255',
            'items.*.code' => 'nullable|string|max:50',
            'items.*.description' => 'nullable|string',
        ], [
            'cabor_id.required' => 'Cabang olahraga wajib dipilih',
            'cabor_id.exists' => 'Cabang olahraga tidak valid',
            'items.required' => 'Minimal 1 kelas harus diisi',
            'items.min' => 'Minimal 1 kelas harus diisi',
            'items.max' => 'Maksimal 50 kelas dalam satu batch',
            'items.*.name.required' => 'Nama kelas wajib diisi',
        ]);

        $createdClasses = [];

        \DB::transaction(function () use ($request, &$createdClasses) {
            foreach ($request->items as $item) {
                $competitionClass = CompetitionClass::create([
                    'cabor_id' => $request->cabor_id,
                    'name' => $item['name'],
                    'code' => $item['code'] ?? null,
                    'description' => $item['description'] ?? null,
                    'is_active' => true,
                ]);
                $createdClasses[] = $competitionClass;
            }
        });

        return response()->json([
            'message' => count($createdClasses) . ' kelas pertandingan berhasil ditambahkan',
            'count' => count($createdClasses),
            'competition_classes' => $createdClasses,
        ], 201);
    }

    /**
     * Display the specified competition class.
     */
    public function show(CompetitionClass $competitionClass)
    {
        $competitionClass->load('cabor:id,name');
        $competitionClass->loadCount('athletes');
        return response()->json($competitionClass);
    }

    /**
     * Update the specified competition class.
     */
    public function update(Request $request, CompetitionClass $competitionClass)
    {
        $this->authorize('competition_classes.edit');
        
        $request->validate([
            'cabor_id' => 'required|exists:cabors,id',
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $competitionClass->update([
            'cabor_id' => $request->cabor_id,
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'is_active' => $request->is_active ?? $competitionClass->is_active,
        ]);

        $competitionClass->load('cabor:id,name');

        return response()->json([
            'message' => 'Kelas pertandingan berhasil diupdate',
            'competition_class' => $competitionClass,
        ]);
    }

    /**
     * Remove the specified competition class.
     */
    public function destroy(CompetitionClass $competitionClass)
    {
        $this->authorize('competition_classes.delete');
        
        // Check if has athletes
        if ($competitionClass->athletes()->count() > 0) {
            return response()->json([
                'message' => 'Tidak dapat menghapus kelas yang memiliki atlet',
            ], 400);
        }

        $competitionClass->delete();

        return response()->json([
            'message' => 'Kelas pertandingan berhasil dihapus',
        ]);
    }

    /**
     * Get all competition classes for dropdown (optionally filtered by cabor).
     */
    public function all(Request $request)
    {
        $query = CompetitionClass::where('is_active', true)
            ->with('cabor:id,name');

        // Filter by cabor if provided
        if ($request->has('cabor_id') && $request->cabor_id) {
            $query->where('cabor_id', $request->cabor_id);
        }

        $classes = $query->orderBy('cabor_id')
            ->orderBy('name')
            ->get(['id', 'cabor_id', 'name', 'code']);

        return response()->json($classes);
    }
}
