<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

/**
 * KUK 025: Debugging - Activity log untuk tracking
 * KUK 020: SQL - Query dengan filtering dan pagination
 */
class LogController extends Controller
{
    /**
     * Display activity logs
     * KUK 022: Algoritma - Filtering dan sorting
     */
    public function index(Request $request)
    {
        // Filter by current user - hanya aktivitas milik user yang login
        $query = ActivityLog::where('user_id', auth()->id());

        // Filter by action (KUK 022: Filter algorithm)
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by date range
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // Search
        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        $logs = $query->latest('created_at')
            ->paginate(50)
            ->withQueryString();

        $actions = ActivityLog::where('user_id', auth()->id())
            ->select('action')
            ->distinct()
            ->pluck('action');

        return view('logs.index', compact('logs', 'actions'));
    }

    /**
     * Clear old logs
     * KUK 047: System maintenance - Cleanup old data
     */
    public function clear(Request $request)
    {
        try {
            $days = $request->get('days', 30);

            $deleted = ActivityLog::where('created_at', '<', now()->subDays($days))
                ->delete();

            return redirect()
                ->route('logs.index')
                ->with('success', "Berhasil menghapus {$deleted} log lama!");

        } catch (\Exception $e) {
            \Log::error('Error clearing logs: ' . $e->getMessage());

            return redirect()
                ->route('logs.index')
                ->with('error', 'Gagal menghapus log: ' . $e->getMessage());
        }
    }

    /**
     * Export logs to file
     * KUK 030: File generation
     */
    public function export(Request $request)
    {
        $logs = ActivityLog::latest()->get();

        $filename = 'activity-logs-' . now()->format('Y-m-d-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');

            // Header
            fputcsv($file, ['Time', 'Action', 'Description', 'IP Address']);

            // Data
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->created_at->format('Y-m-d H:i:s'),
                    $log->action,
                    $log->description,
                    $log->ip_address,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
