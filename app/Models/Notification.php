<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Notification
 *
 * An in-app notification bell entry for a specific user (e.g. "New
 * appointment request", "Visit finalized"). These are created via
 * App\Helpers\NotificationHelper — see that class for the actual
 * "when does X happen" trigger logic; this model is just the storage +
 * read/unread bookkeeping.
 */
class Notification extends Model
{
    protected $fillable = [
        'user_id', 'type', 'title', 'message',
        'icon', 'color', 'url', 'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    // ── Relationships ─────────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── Helpers ───────────────────────────────────────────────
    public function isUnread(): bool
    {
        return $this->read_at === null;
    }

    public function markAsRead(): void
    {
        if ($this->isUnread()) {
            $this->update(['read_at' => now()]);
        }
    }

    // ── Scopes ────────────────────────────────────────────────
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeRecent($query)
    {
        return $query->latest()->limit(20);
    }
}