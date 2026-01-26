<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\ErrorLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ActivityLogController extends Controller
{
    /**
     * Get paginated list of activity logs with filters.
     */
    public function index(Request $request): JsonResponse
    {
        $query = ActivityLog::with('user')
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('user_id')) {
            $query->byUser($request->user_id);
        }

        if ($request->filled('model')) {
            $query->byModel($request->model);
        }

        if ($request->filled('action')) {
            $query->byAction($request->action);
        }

        if ($request->filled('date_from') || $request->filled('date_to')) {
            $query->byDateRange($request->date_from, $request->date_to);
        }

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $perPage = $request->get('per_page', 20);
        $logs = $query->paginate($perPage);

        return response()->json($logs);
    }

    /**
     * Get a single activity log detail.
     */
    public function show(int $id): JsonResponse
    {
        $log = ActivityLog::with('user')->findOrFail($id);

        return response()->json($log);
    }

    /**
     * Get dashboard statistics.
     */
    public function stats(): JsonResponse
    {
        $stats = [
            'total' => ActivityLog::count(),
            'today' => ActivityLog::today()->count(),
            'this_week' => ActivityLog::where('created_at', '>=', now()->startOfWeek())->count(),
            'by_action' => [
                'created' => ActivityLog::byAction('created')->count(),
                'updated' => ActivityLog::byAction('updated')->count(),
                'deleted' => ActivityLog::byAction('deleted')->count(),
            ],
            'models' => ActivityLog::getDistinctModels(),
            'top_users' => ActivityLog::selectRaw('user_id, user_name, COUNT(*) as count')
                ->whereNotNull('user_id')
                ->groupBy('user_id', 'user_name')
                ->orderByDesc('count')
                ->limit(5)
                ->get(),
            'recent_models' => ActivityLog::selectRaw('model_name, COUNT(*) as count')
                ->groupBy('model_name')
                ->orderByDesc('count')
                ->limit(5)
                ->get(),
        ];

        return response()->json($stats);
    }

    /**
     * Export activity logs to CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        $query = ActivityLog::with('user')
            ->orderBy('created_at', 'desc');

        // Apply same filters as index
        if ($request->filled('user_id')) {
            $query->byUser($request->user_id);
        }
        if ($request->filled('model')) {
            $query->byModel($request->model);
        }
        if ($request->filled('action')) {
            $query->byAction($request->action);
        }
        if ($request->filled('date_from') || $request->filled('date_to')) {
            $query->byDateRange($request->date_from, $request->date_to);
        }

        $logs = $query->limit(5000)->get(); // Limit export to 5000 rows

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="activity_logs_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($logs) {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, [
                'ID',
                'Waktu',
                'User',
                'Aksi',
                'Model',
                'Record ID',
                'Nama Record',
                'Field Berubah',
                'IP Address',
            ]);

            // Data rows
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->created_at->format('Y-m-d H:i:s'),
                    $log->user_name ?? 'System',
                    $log->action_label,
                    $log->model_name,
                    $log->model_id,
                    $log->record_name ?? '-',
                    implode(', ', $log->changed_fields ?? []),
                    $log->ip_address ?? '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Cleanup old activity logs.
     */
    public function cleanup(Request $request): JsonResponse
    {
        $days = $request->get('days', 90);
        
        $deleted = ActivityLog::where('created_at', '<', now()->subDays($days))->delete();

        return response()->json([
            'message' => "Berhasil menghapus {$deleted} log yang lebih dari {$days} hari.",
            'deleted_count' => $deleted,
        ]);
    }

    /**
     * Get users for filter dropdown.
     */
    public function users(): JsonResponse
    {
        $users = User::select('id', 'name', 'email')
            ->whereIn('id', ActivityLog::distinct()->pluck('user_id'))
            ->orderBy('name')
            ->get();

        return response()->json($users);
    }

    // ==========================================
    // ERROR LOG METHODS
    // ==========================================

    /**
     * Get paginated list of error logs with filters.
     */
    public function errorIndex(Request $request): JsonResponse
    {
        $query = ErrorLog::with('user')
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        if ($request->filled('severity')) {
            $query->bySeverity($request->severity);
        }

        if ($request->filled('is_resolved')) {
            if ($request->is_resolved === 'true' || $request->is_resolved === '1') {
                $query->resolved();
            } else {
                $query->unresolved();
            }
        }

        if ($request->filled('date_from') || $request->filled('date_to')) {
            $query->byDateRange($request->date_from, $request->date_to);
        }

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $perPage = $request->get('per_page', 20);
        $logs = $query->paginate($perPage);

        return response()->json($logs);
    }

    /**
     * Get error log statistics.
     */
    public function errorStats(): JsonResponse
    {
        $stats = [
            'total' => ErrorLog::count(),
            'unresolved' => ErrorLog::unresolved()->count(),
            'today' => ErrorLog::today()->count(),
            'by_severity' => [
                'critical' => ErrorLog::bySeverity('critical')->count(),
                'error' => ErrorLog::bySeverity('error')->count(),
                'warning' => ErrorLog::bySeverity('warning')->count(),
                'info' => ErrorLog::bySeverity('info')->count(),
            ],
            'types' => ErrorLog::getDistinctTypes(),
            'type_labels' => ErrorLog::TYPE_LABELS,
            'severity_labels' => ErrorLog::SEVERITY_LABELS,
        ];

        return response()->json($stats);
    }

    /**
     * Get a single error log detail.
     */
    public function errorShow(int $id): JsonResponse
    {
        $log = ErrorLog::with(['user', 'resolver'])->findOrFail($id);

        return response()->json($log);
    }

    /**
     * Mark an error as resolved.
     */
    public function resolveError(Request $request, int $id): JsonResponse
    {
        $log = ErrorLog::findOrFail($id);
        
        $log->markResolved($request->notes);

        return response()->json([
            'message' => 'Error berhasil ditandai sebagai resolved.',
            'log' => $log->fresh(['user', 'resolver']),
        ]);
    }

    /**
     * Cleanup old error logs.
     */
    public function errorCleanup(Request $request): JsonResponse
    {
        $days = $request->get('days', 90);
        
        $deleted = ErrorLog::where('created_at', '<', now()->subDays($days))->delete();

        return response()->json([
            'message' => "Berhasil menghapus {$deleted} error log yang lebih dari {$days} hari.",
            'deleted_count' => $deleted,
        ]);
    }
}
