<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventAthlete;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class EventController extends Controller
{
    /**
     * Display a listing of events.
     */
    public function index(Request $request)
    {
        $query = Event::withCount('athletes');

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        // Filter by type
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        // Filter by year
        if ($request->has('year') && $request->year) {
            $query->where('year', $request->year);
        }

        // Filter by status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // Pagination
        $perPage = $request->get('per_page', 10);
        $events = $query->orderBy('start_date', 'desc')->paginate($perPage);

        return response()->json($events);
    }

    /**
     * Store a newly created event.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:provincial,national,international',
            'year' => 'required|integer|min:2000|max:2100',
            'location' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'registration_start' => 'nullable|date',
            'registration_end' => 'nullable|date|after_or_equal:registration_start',
            'is_active' => 'boolean',
        ], [
            'name.required' => 'Nama event wajib diisi',
            'type.required' => 'Tipe event wajib dipilih',
            'year.required' => 'Tahun event wajib diisi',
        ]);

        $validated['slug'] = Str::slug($validated['name'] . '-' . $validated['year']);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('events', 'public');
        }

        $event = Event::create($validated);

        return response()->json([
            'message' => 'Event berhasil dibuat',
            'event' => $event,
        ], 201);
    }

    /**
     * Display the specified event.
     */
    public function show(Event $event)
    {
        $event->load(['eventAthletes.athlete', 'eventAthletes.cabor']);
        $event->loadCount('athletes');
        
        return response()->json($event);
    }

    /**
     * Update the specified event.
     */
    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:provincial,national,international',
            'year' => 'required|integer|min:2000|max:2100',
            'location' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'registration_start' => 'nullable|date',
            'registration_end' => 'nullable|date|after_or_equal:registration_start',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name'] . '-' . $validated['year']);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            if ($event->logo) {
                Storage::disk('public')->delete($event->logo);
            }
            $validated['logo'] = $request->file('logo')->store('events', 'public');
        }

        $event->update($validated);

        return response()->json([
            'message' => 'Event berhasil diupdate',
            'event' => $event,
        ]);
    }

    /**
     * Remove the specified event.
     */
    public function destroy(Event $event)
    {
        if ($event->logo) {
            Storage::disk('public')->delete($event->logo);
        }

        $event->delete();

        return response()->json([
            'message' => 'Event berhasil dihapus',
        ]);
    }

    /**
     * Get athletes in this event.
     */
    public function athletes(Event $event, Request $request)
    {
        $query = $event->eventAthletes()->with(['athlete', 'cabor', 'competitionClass']);

        // Filter by cabor
        if ($request->has('cabor_id') && $request->cabor_id) {
            $query->where('cabor_id', $request->cabor_id);
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $athletes = $query->get();

        return response()->json($athletes);
    }

    /**
     * Register an athlete to this event.
     * Rules:
     * - 1 athlete can only participate in 1 cabor per event
     * - 1 athlete can register for max 2 competition classes in that cabor
     */
    public function registerAthlete(Request $request, Event $event)
    {
        $validated = $request->validate([
            'athlete_id' => 'required|exists:athletes,id',
            'cabor_id' => 'required|exists:cabors,id',
            'competition_class_id' => 'nullable|exists:competition_classes,id',
            'notes' => 'nullable|string',
        ], [
            'athlete_id.required' => 'Atlet wajib dipilih',
            'cabor_id.required' => 'Cabor wajib dipilih',
        ]);

        $athleteId = $validated['athlete_id'];
        $caborId = $validated['cabor_id'];
        $classId = $validated['competition_class_id'] ?? null;

        // Check if athlete already registered in this event with DIFFERENT cabor
        $existingInOtherCabor = EventAthlete::where('event_id', $event->id)
            ->where('athlete_id', $athleteId)
            ->where('cabor_id', '!=', $caborId)
            ->exists();

        if ($existingInOtherCabor) {
            return response()->json([
                'message' => 'Atlet sudah terdaftar di cabor lain. Satu atlet hanya boleh mengikuti 1 cabor per event.',
            ], 422);
        }

        // Check if athlete already registered for this specific class
        $existingInSameClass = EventAthlete::where('event_id', $event->id)
            ->where('athlete_id', $athleteId)
            ->where('competition_class_id', $classId)
            ->exists();

        if ($existingInSameClass) {
            return response()->json([
                'message' => 'Atlet sudah terdaftar di kelas pertandingan ini.',
            ], 422);
        }

        // Check if athlete already has 2 registrations in this cabor
        $countInCabor = EventAthlete::where('event_id', $event->id)
            ->where('athlete_id', $athleteId)
            ->where('cabor_id', $caborId)
            ->count();

        if ($countInCabor >= 2) {
            return response()->json([
                'message' => 'Atlet sudah terdaftar di 2 kelas pertandingan. Maksimal 2 kelas per cabor.',
            ], 422);
        }

        $eventAthlete = EventAthlete::create([
            'event_id' => $event->id,
            'athlete_id' => $athleteId,
            'cabor_id' => $caborId,
            'competition_class_id' => $classId,
            'status' => 'registered',
            'notes' => $validated['notes'] ?? null,
        ]);

        $eventAthlete->load(['athlete', 'cabor', 'competitionClass']);

        return response()->json([
            'message' => 'Atlet berhasil didaftarkan ke event',
            'event_athlete' => $eventAthlete,
        ], 201);
    }

    /**
     * Remove an athlete registration from this event.
     * Uses eventAthleteId (the ID of the event_athlete pivot record)
     */
    public function removeAthlete(Event $event, $eventAthleteId)
    {
        $eventAthlete = EventAthlete::where('event_id', $event->id)
            ->where('id', $eventAthleteId)
            ->first();

        if (!$eventAthlete) {
            return response()->json([
                'message' => 'Registrasi atlet tidak ditemukan di event ini',
            ], 404);
        }

        $eventAthlete->delete();

        return response()->json([
            'message' => 'Registrasi atlet berhasil dihapus dari event',
        ]);
    }

    /**
     * Update athlete status in event.
     * Uses eventAthleteId (the ID of the event_athlete pivot record)
     */
    public function updateAthleteStatus(Request $request, Event $event, $eventAthleteId)
    {
        $validated = $request->validate([
            'status' => 'required|in:registered,verified,rejected',
            'notes' => 'nullable|string',
        ]);

        $eventAthlete = EventAthlete::where('event_id', $event->id)
            ->where('id', $eventAthleteId)
            ->first();

        if (!$eventAthlete) {
            return response()->json([
                'message' => 'Registrasi atlet tidak ditemukan di event ini',
            ], 404);
        }

        $eventAthlete->update($validated);

        return response()->json([
            'message' => 'Status atlet berhasil diupdate',
            'event_athlete' => $eventAthlete->load(['athlete', 'cabor', 'competitionClass']),
        ]);
    }

    /**
     * Get all events for dropdown.
     */
    public function all()
    {
        $events = Event::active()
            ->orderBy('year', 'desc')
            ->get(['id', 'name', 'year', 'type']);

        return response()->json($events);
    }
}
