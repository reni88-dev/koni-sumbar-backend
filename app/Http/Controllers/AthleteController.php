<?php

namespace App\Http\Controllers;

use App\Models\Athlete;
use App\Rules\UniqueNikHash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AthleteController extends Controller
{
    /**
     * Display a listing of athletes.
     */
    public function index(Request $request)
    {
        $query = Athlete::with(['cabor', 'educationLevel', 'competitionClass']);

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        // Filter by cabor
        if ($request->has('cabor_id') && $request->cabor_id) {
            $query->where('cabor_id', $request->cabor_id);
        }

        // Filter by gender
        if ($request->has('gender') && $request->gender) {
            $query->where('gender', $request->gender);
        }

        // Filter by status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Pagination
        $perPage = $request->get('per_page', 10);
        $athletes = $query->orderBy('name')->paginate($perPage);

        return response()->json($athletes);
    }

    /**
     * Store a newly created athlete.
     */
    public function store(Request $request)
    {
        // Parse JSON string fields
        $data = $request->all();
        if (isset($data['top_achievements']) && is_string($data['top_achievements'])) {
            // Decode HTML entities first (e.g., &quot; to "), then parse JSON
            $data['top_achievements'] = json_decode(html_entity_decode($data['top_achievements']), true) ?? [];
        }
        if (isset($data['provincial_achievements']) && is_string($data['provincial_achievements'])) {
            $data['provincial_achievements'] = json_decode(html_entity_decode($data['provincial_achievements']), true) ?? [];
        }
        if (isset($data['national_achievements']) && is_string($data['national_achievements'])) {
            $data['national_achievements'] = json_decode(html_entity_decode($data['national_achievements']), true) ?? [];
        }
        if (isset($data['international_achievements']) && is_string($data['international_achievements'])) {
            $data['international_achievements'] = json_decode(html_entity_decode($data['international_achievements']), true) ?? [];
        }
        
        $request->replace($data);

        $validated = $request->validate([
            'cabor_id' => 'required|exists:cabors,id',
            'education_level_id' => 'required|exists:education_levels,id',
            'name' => 'required|string|max:255',
            'nik' => ['required', 'string', 'digits:16', new UniqueNikHash()],
            'no_kk' => 'required|string|digits:16',
            'competition_class_id' => 'required|exists:competition_classes,id',
            'birth_place' => 'required|string|max:100',
            'birth_date' => 'required|date',
            'gender' => 'required|in:male,female',
            'religion' => 'required|string|max:50',
            'address' => 'required|string',
            'blood_type' => 'required|in:A,B,AB,O',
            'occupation' => 'required|string|max:100',
            'marital_status' => 'required|in:single,married,divorced,widowed',
            'hobby' => 'nullable|string|max:255',
            'height' => 'required|integer|min:50|max:300',
            'weight' => 'required|numeric|min:20|max:300',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:100',
            'career_start_year' => 'required|integer|min:1950|max:' . date('Y'),
            'injury_illness_history' => 'nullable|string',
            'top_achievements' => 'nullable|array',
            'provincial_achievements' => 'nullable|array',
            'national_achievements' => 'nullable|array',
            'international_achievements' => 'nullable|array',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_active' => 'boolean',
        ], [
            'cabor_id.required' => 'Cabang olahraga wajib dipilih',
            'education_level_id.required' => 'Pendidikan wajib dipilih',
            'name.required' => 'Nama atlet wajib diisi',
            'nik.required' => 'NIK wajib diisi',
            'nik.digits' => 'NIK harus 16 digit',
            'no_kk.required' => 'No. KK wajib diisi',
            'no_kk.digits' => 'No. KK harus 16 digit',
            'competition_class_id.required' => 'Kelas pertandingan wajib dipilih',
            'competition_class_id.exists' => 'Kelas pertandingan tidak valid',
            'birth_place.required' => 'Tempat lahir wajib diisi',
            'birth_date.required' => 'Tanggal lahir wajib diisi',
            'gender.required' => 'Jenis kelamin wajib dipilih',
            'religion.required' => 'Agama wajib dipilih',
            'address.required' => 'Alamat wajib diisi',
            'blood_type.required' => 'Golongan darah wajib dipilih',
            'occupation.required' => 'Pekerjaan wajib diisi',
            'marital_status.required' => 'Status perkawinan wajib dipilih',
            'height.required' => 'Tinggi badan wajib diisi',
            'weight.required' => 'Berat badan wajib diisi',
            'phone.required' => 'No. telepon wajib diisi',
            'email.required' => 'Email wajib diisi',
            'career_start_year.required' => 'Tahun mulai karir wajib diisi',
            'top_achievements.required' => 'Minimal 1 prestasi tertinggi wajib diisi',
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('athletes', 'public');
        }

        // Ensure top_achievements is always set from parsed data
        $validated['top_achievements'] = $data['top_achievements'] ?? [];

        $athlete = Athlete::create($validated);
        $athlete->load(['cabor', 'educationLevel', 'competitionClass']);

        return response()->json([
            'message' => 'Atlet berhasil ditambahkan',
            'athlete' => $athlete,
        ], 201);
    }

    /**
     * Display the specified athlete.
     */
    public function show(Athlete $athlete)
    {
        $athlete->load(['cabor', 'educationLevel', 'competitionClass', 'events']);
        
        return response()->json($athlete);
    }

    /**
     * Update the specified athlete.
     */
    public function update(Request $request, Athlete $athlete)
    {
        // Parse JSON string fields
        $data = $request->all();
        
        if (isset($data['top_achievements']) && is_string($data['top_achievements'])) {
            // Decode HTML entities first (e.g., &quot; to "), then parse JSON
            $data['top_achievements'] = json_decode(html_entity_decode($data['top_achievements']), true) ?? [];
        }
        if (isset($data['provincial_achievements']) && is_string($data['provincial_achievements'])) {
            $data['provincial_achievements'] = json_decode(html_entity_decode($data['provincial_achievements']), true) ?? [];
        }
        if (isset($data['national_achievements']) && is_string($data['national_achievements'])) {
            $data['national_achievements'] = json_decode(html_entity_decode($data['national_achievements']), true) ?? [];
        }
        if (isset($data['international_achievements']) && is_string($data['international_achievements'])) {
            $data['international_achievements'] = json_decode(html_entity_decode($data['international_achievements']), true) ?? [];
        }
        
        $request->replace($data);

        $validated = $request->validate([
            'cabor_id' => 'required|exists:cabors,id',
            'education_level_id' => 'required|exists:education_levels,id',
            'name' => 'required|string|max:255',
            'nik' => ['required', 'string', 'digits:16', new UniqueNikHash($athlete->id)],
            'no_kk' => 'required|string|digits:16',
            'competition_class_id' => 'required|exists:competition_classes,id',
            'birth_place' => 'required|string|max:100',
            'birth_date' => 'required|date',
            'gender' => 'required|in:male,female',
            'religion' => 'required|string|max:50',
            'address' => 'required|string',
            'blood_type' => 'required|in:A,B,AB,O',
            'occupation' => 'required|string|max:100',
            'marital_status' => 'required|in:single,married,divorced,widowed',
            'hobby' => 'nullable|string|max:255',
            'height' => 'required|integer|min:50|max:300',
            'weight' => 'required|numeric|min:20|max:300',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:100',
            'career_start_year' => 'required|integer|min:1950|max:' . date('Y'),
            'injury_illness_history' => 'nullable|string',
            'top_achievements' => 'nullable|array',
            'provincial_achievements' => 'nullable|array',
            'national_achievements' => 'nullable|array',
            'international_achievements' => 'nullable|array',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_active' => 'boolean',
        ], [
            'cabor_id.required' => 'Cabang olahraga wajib dipilih',
            'education_level_id.required' => 'Pendidikan wajib dipilih',
            'name.required' => 'Nama atlet wajib diisi',
            'nik.required' => 'NIK wajib diisi',
            'nik.digits' => 'NIK harus 16 digit',
            'no_kk.required' => 'No. KK wajib diisi',
            'no_kk.digits' => 'No. KK harus 16 digit',
            'competition_class_id.required' => 'Kelas pertandingan wajib dipilih',
            'competition_class_id.exists' => 'Kelas pertandingan tidak valid',
            'birth_place.required' => 'Tempat lahir wajib diisi',
            'birth_date.required' => 'Tanggal lahir wajib diisi',
            'gender.required' => 'Jenis kelamin wajib dipilih',
            'religion.required' => 'Agama wajib dipilih',
            'address.required' => 'Alamat wajib diisi',
            'blood_type.required' => 'Golongan darah wajib dipilih',
            'occupation.required' => 'Pekerjaan wajib diisi',
            'marital_status.required' => 'Status perkawinan wajib dipilih',
            'height.required' => 'Tinggi badan wajib diisi',
            'weight.required' => 'Berat badan wajib diisi',
            'phone.required' => 'No. telepon wajib diisi',
            'email.required' => 'Email wajib diisi',
            'career_start_year.required' => 'Tahun mulai karir wajib diisi',
            'top_achievements.required' => 'Minimal 1 prestasi tertinggi wajib diisi',
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            if ($athlete->photo) {
                Storage::disk('public')->delete($athlete->photo);
            }
            $validated['photo'] = $request->file('photo')->store('athletes', 'public');
        }

        // Ensure top_achievements is always set from parsed data
        $validated['top_achievements'] = $data['top_achievements'] ?? [];

        $athlete->update($validated);
        $athlete->load(['cabor', 'educationLevel', 'competitionClass']);

        return response()->json([
            'message' => 'Atlet berhasil diupdate',
            'athlete' => $athlete,
        ]);
    }

    /**
     * Remove the specified athlete.
     */
    public function destroy(Athlete $athlete)
    {
        if ($athlete->photo) {
            Storage::disk('public')->delete($athlete->photo);
        }

        $athlete->delete();

        return response()->json([
            'message' => 'Atlet berhasil dihapus',
        ]);
    }

    /**
     * Get events this athlete participates in.
     */
    public function events(Athlete $athlete)
    {
        $events = $athlete->events()->with('pivot.cabor')->get();
        
        return response()->json($events);
    }

    /**
     * Get all athletes for dropdown.
     */
    public function all(Request $request)
    {
        $query = Athlete::active()->with('cabor');

        // Filter by cabor if provided
        if ($request->has('cabor_id') && $request->cabor_id) {
            $query->where('cabor_id', $request->cabor_id);
        }

        $athletes = $query->orderBy('name')->get(['id', 'name', 'cabor_id', 'nik']);

        return response()->json($athletes);
    }
}
