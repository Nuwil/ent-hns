<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Appointment
 *
 * A requested/booked slot for a patient to see a doctor. Lifecycle:
 *
 *   pending ──accept──> accepted ──(visit finalized)──> completed
 *      │                    │
 *      └──────cancel────────┘
 *
 * When a secretary/doctor accepts a pending appointment, its id is
 * carried into the resulting Visit (see AppointmentController::confirm())
 * so the visit can later report its *original* appointment date even if
 * the consultation itself happens on a different day
 * (see Visit::cameFromAcceptedAppointment()).
 */
class Appointment extends Model
{
    use HasFactory;

    // Status constants — use these instead of raw strings everywhere.
    const STATUS_PENDING  = 'pending';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'scheduled_at',
        'reason',
        'status',
        'notes',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    // ── Relationships ─────────────────────────────────────────────

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    // ── Query scopes ─────────────────────────────────────────────
    // Chainable filters, e.g. Appointment::pending()->today()->get()

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', self::STATUS_ACCEPTED);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('scheduled_at', today());
    }

    public function scopeUpcoming($query)
    {
        return $query->where('scheduled_at', '>=', now())->orderBy('scheduled_at');
    }

    // ── Status helpers ────────────────────────────────────────────

    public function isPending(): bool   { return $this->status === self::STATUS_PENDING; }
    public function isAccepted(): bool  { return $this->status === self::STATUS_ACCEPTED; }
    public function isCancelled(): bool { return $this->status === self::STATUS_CANCELLED; }
    public function isCompleted(): bool { return $this->status === self::STATUS_COMPLETED; }

    /** Bootstrap badge class matching the current status, used in Blade views. */
    public function statusBadgeClass(): string
    {
        return match($this->status) {
            self::STATUS_PENDING   => 'badge-warning',
            self::STATUS_ACCEPTED  => 'badge-info',
            self::STATUS_COMPLETED => 'badge-success',
            self::STATUS_CANCELLED => 'badge-danger',
            default                => 'badge-secondary',
        };
    }
}
