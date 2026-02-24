<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * KUK 018: OOP - Activity Log pattern untuk audit trail
 * KUK 025: Debugging - Logging untuk tracking errors dan activities
 */
class ActivityLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'description',
        'properties',
        'ip_address',
        'user_agent',
        'created_at'
    ];

    protected $casts = [
        'properties' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Static method untuk create log entry
     * KUK 022: Algoritma - Static factory method pattern
     */
    public static function log($action, $model, $description, $properties = null)
    {
        return self::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'description' => $description,
            'properties' => $properties,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);
    }

    /**
     * Get related model
     * KUK 018: Polymorphic relationship
     */
    public function model()
    {
        return $this->morphTo();
    }

    /**
     * Get user who performed the action
     * KUK 018: OOP - Relationship
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Format created at time for humans
     * KUK 022: String formatting algorithm
     */
    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Get action badge color
     * KUK 006: UX Design - Visual feedback
     */
    public function getActionColorAttribute()
    {
        return match($this->action) {
            'create' => 'green',
            'update' => 'blue',
            'delete' => 'red',
            'download' => 'purple',
            'view' => 'gray',
            default => 'gray',
        };
    }
}
