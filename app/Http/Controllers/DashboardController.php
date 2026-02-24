<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Document;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * KUK 018: OOP - Controller dengan structured programming
 * KUK 017: Terstruktur - MVC pattern implementation
 */
class DashboardController extends Controller
{
    /**
     * Display dashboard
     * KUK 020: SQL - Complex queries dengan aggregation
     * KUK 022: Algoritma - Data processing dan statistics
     */
    public function index()
    {
        // Statistics - hanya dokumen milik user yang login
        $totalDocuments = Document::where('user_id', auth()->id())->count();
        $activeDocuments = Document::where('user_id', auth()->id())
            ->where('status', 'active')
            ->count();
        $totalCategories = Category::count();
        $totalSize = Document::where('user_id', auth()->id())->sum('file_size') ?? 0;

        // Recent documents - hanya milik user yang login
        $recentDocuments = Document::with('category')
            ->where('user_id', auth()->id())
            ->latest()
            ->take(10)
            ->get();

        // Categories with document count - hanya dokumen milik user yang login
        $categories = Category::withCount(['documents' => function ($query) {
                $query->where('user_id', auth()->id());
            }])
            ->orderBy('name')
            ->get();

        // Recent activity logs - hanya aktivitas user yang login
        $recentActivities = ActivityLog::where('user_id', auth()->id())
            ->latest()
            ->take(10)
            ->get();

        // Check for expiring documents
        $this->checkExpiringDocuments();

        return view('dashboard', compact(
            'totalDocuments',
            'activeDocuments',
            'totalCategories',
            'totalSize',
            'recentDocuments',
            'categories',
            'recentActivities'
        ));
    }

    /**
     * Check for expiring documents and create notifications
     * KUK 044: Alert/Notification system
     * KUK 022: Algoritma - Date comparison
     */
    private function checkExpiringDocuments()
    {
        $expiringDocuments = Document::where('user_id', auth()->id())
            ->whereNotNull('expiry_date')
            ->where('status', 'active')
            ->whereBetween('expiry_date', [now(), now()->addDays(30)])
            ->get();

        foreach ($expiringDocuments as $doc) {
            $doc->checkExpiryNotification();
        }
    }

    /**
     * Get system info for monitoring
     * KUK 045: Monitor - Resource monitoring
     */
    public function getSystemInfo()
    {
        // Disk usage
        $diskTotal = disk_total_space(storage_path());
        $diskFree = disk_free_space(storage_path());
        $diskUsed = $diskTotal - $diskFree;

        // Storage usage by documents
        $storageUsed = Document::sum('file_size');

        return response()->json([
            'disk' => [
                'total' => $this->formatBytes($diskTotal),
                'used' => $this->formatBytes($diskUsed),
                'free' => $this->formatBytes($diskFree),
                'percentage' => round(($diskUsed / $diskTotal) * 100, 2),
            ],
            'storage' => [
                'documents_size' => $this->formatBytes($storageUsed),
                'documents_count' => Document::count(),
            ],
            'memory' => [
                'used' => $this->formatBytes(memory_get_usage()),
                'peak' => $this->formatBytes(memory_get_peak_usage()),
            ],
        ]);
    }

    /**
     * Format bytes to human readable
     * KUK 022: Algoritma - Format conversion
     */
    private function formatBytes($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}
