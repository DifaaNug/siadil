<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

/**
 * KUK 018: OOP - Model class dengan inheritance dan encapsulation
 * KUK 030: Multimedia - File upload handling
 */
class Document extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'user_id',
        'title',
        'document_number',
        'description',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'document_date',
        'expiry_date',
        'status',
        'tags',
        'download_count'
    ];

    protected $casts = [
        'document_date' => 'date',
        'expiry_date' => 'date',
        'tags' => 'array',
        'file_size' => 'integer',
        'download_count' => 'integer',
    ];

    /**
     * Boot method untuk lifecycle hooks
     */
    protected static function boot()
    {
        parent::boot();

        // Update category count saat document dibuat
        static::created(function ($document) {
            $document->category->updateDocumentCount();
            ActivityLog::log('create', $document, 'Dokumen baru ditambahkan: ' . $document->title);
        });

        // Update category count saat document dihapus
        static::deleted(function ($document) {
            $document->category->updateDocumentCount();
            ActivityLog::log('delete', $document, 'Dokumen dihapus: ' . $document->title);
        });

        // Check expiry date untuk notifikasi
        static::updated(function ($document) {
            $document->checkExpiryNotification();
            ActivityLog::log('update', $document, 'Dokumen diupdate: ' . $document->title);
        });
    }

    /**
     * Relationship ke category
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get file size in human readable format
     * KUK 022: Algoritma - Format conversion
     */
    public function getFileSizeHumanAttribute()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get file URL
     * KUK 045: Resource monitoring - File path management
     */
    public function getFileUrlAttribute()
    {
        // Return URL untuk storage public
        return asset('storage/' . $this->file_path);
    }

    /**
     * Increment download count
     * KUK 022: Algoritma increment counter
     */
    public function incrementDownloadCount()
    {
        $this->increment('download_count');
        ActivityLog::log('download', $this, 'Dokumen didownload: ' . $this->title);
    }

    /**
     * Check if document is expiring soon
     * KUK 044: Alert notification logic
     */
    public function checkExpiryNotification()
    {
        if ($this->expiry_date) {
            $daysUntilExpiry = (int) ceil(now()->diffInDays($this->expiry_date, false));

            // Update status jika sudah expired
            if ($daysUntilExpiry < 0 && $this->status !== 'expired') {
                $this->update(['status' => 'expired']);
            }
        }
    }

    /**
     * Scope untuk search
     * KUK 020: SQL - Query optimization
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('document_number', 'like', "%{$search}%");
        });
    }

    /**
     * Scope filter by status
     */
    public function scopeStatus($query, $status)
    {
        if ($status) {
            return $query->where('status', $status);
        }
        return $query;
    }

    /**
     * Scope filter by category
     */
    public function scopeCategory($query, $categoryId)
    {
        if ($categoryId) {
            return $query->where('category_id', $categoryId);
        }
        return $query;
    }
}
