<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * KUK 018: OOP - Model dengan encapsulation dan relationships
 * KUK 021: Akses Basis Data - Eloquent ORM untuk database operations
 */
class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
        'document_count'
    ];

    protected $casts = [
        'document_count' => 'integer',
    ];

    /**
     * Boot method untuk auto-generate slug
     * KUK 022: Algoritma - String manipulation
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });

        // Update document count saat category dihapus
        static::deleting(function ($category) {
            $category->documents()->delete();
        });
    }

    /**
     * Relationship ke documents
     * KUK 018: OOP Relationship pattern
     */
    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Get active documents count
     * KUK 022: Algoritma counting
     */
    public function getActiveDocumentsCountAttribute()
    {
        return $this->documents()->where('status', 'active')->count();
    }

    /**
     * Update cached document count
     * KUK 047: Update system - Cache invalidation
     */
    public function updateDocumentCount()
    {
        $this->document_count = $this->documents()->count();
        $this->save();
    }
}
