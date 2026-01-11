<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Cabor;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CaborController extends Controller
{
    /**
     * Display a listing of cabors.
     */
    public function index(Request $request)
    {
        $query = Cabor::withCount('athletes');

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('federation', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // Pagination
        $perPage = $request->get('per_page', 10);
        $cabors = $query->orderBy('name')->paginate($perPage);

        return response()->json($cabors);
    }

    /**
     * Store a newly created cabor.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'federation' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
        ], [
            'name.required' => 'Nama cabor wajib diisi',
            'logo.image' => 'File harus berupa gambar',
            'logo.max' => 'Ukuran logo maksimal 2MB',
        ]);

        $data = [
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'federation' => $request->federation,
            'is_active' => $request->is_active ?? true,
        ];

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('cabors', 'public');
        }

        $cabor = Cabor::create($data);

        return response()->json([
            'message' => 'Cabor berhasil ditambahkan',
            'cabor' => $cabor,
        ], 201);
    }

    /**
     * Display the specified cabor.
     */
    public function show(Cabor $cabor)
    {
        $cabor->loadCount('athletes');
        return response()->json($cabor);
    }

    /**
     * Update the specified cabor.
     */
    public function update(Request $request, Cabor $cabor)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'federation' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
        ]);

        $data = [
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'federation' => $request->federation,
            'is_active' => $request->is_active ?? $cabor->is_active,
        ];

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo
            if ($cabor->logo) {
                Storage::disk('public')->delete($cabor->logo);
            }
            $data['logo'] = $request->file('logo')->store('cabors', 'public');
        }

        $cabor->update($data);

        return response()->json([
            'message' => 'Cabor berhasil diupdate',
            'cabor' => $cabor,
        ]);
    }

    /**
     * Remove the specified cabor.
     */
    public function destroy(Cabor $cabor)
    {
        // Check if cabor has athletes
        if ($cabor->athletes()->count() > 0) {
            return response()->json([
                'message' => 'Tidak dapat menghapus cabor yang memiliki atlet',
            ], 400);
        }

        // Delete logo
        if ($cabor->logo) {
            Storage::disk('public')->delete($cabor->logo);
        }

        $cabor->delete();

        return response()->json([
            'message' => 'Cabor berhasil dihapus',
        ]);
    }

    /**
     * Get all cabors for dropdown.
     */
    public function all()
    {
        $cabors = Cabor::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        return response()->json($cabors);
    }
}
