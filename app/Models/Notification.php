<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * KUK 044: Alert/Notification system
 * KUK 018: OOP - Model dengan business logic
 */
class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'title',
        'message',
        'link',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    /**
     * Mark notification as read
     * KUK 047: Update system
     */
    public function markAsRead()
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }

    /**
     * Scope untuk unread notifications
     * KUK 020: SQL Query scope
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Get notification icon based on type
     * KUK 006: UX Design
     */
    public function getIconAttribute()
    {
        return match($this->type) {
            'expiry_warning' => '⚠️',
            'new_document' => '📄',
            'system' => '🔔',
            default => '📌',
        };
    }

    /**
     * Get notification color
     */
    public function getColorAttribute()
    {
        return match($this->type) {
            'expiry_warning' => 'red',
            'new_document' => 'green',
            'system' => 'blue',
            default => 'gray',
        };
    }
}
