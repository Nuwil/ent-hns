<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    use HasFactory;

    const STATUS_PENDING     = 'pending';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_FINALIZED   = 'finalized';

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'appointment_id',
        'visited_at',
        // Secretary fields
        'chief_complaint',
        'ent_classification',
        // Doctor fields
        'history_of_illness',
        'exam_findings',
        'diagnosis',
        'treatment_plan',
        'notes',
        'prescriptions',
        'follow_up_date',
        // Workflow
        'recorded_by',
        'status',
        'finalized_by',
        'finalized_at',
    ];

    protected $casts = [
        'visited_at'    => 'datetime',
        'finalized_at'  => 'datetime',
        'follow_up_date' => 'date',
        'prescriptions' => 'array',
    ];

    // ── Status helpers ────────────────────────────────────────────

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isInProgress(): bool
    {
        return $this->status === self::STATUS_IN_PROGRESS;
    }

    public function isFinalized(): bool
    {
        return $this->status === self::STATUS_FINALIZED;
    }

    public function isLocked(): bool
    {
        return $this->isFinalized();
    }

    /** Can the secretary still edit this visit? */
    public function secretaryCanEdit(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /** Can the doctor edit/complete this visit? */
    public function doctorCanEdit(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_IN_PROGRESS]);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING     => 'Awaiting Doctor',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_FINALIZED   => 'Finalized',
            default                  => ucfirst($this->status),
        };
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING     => 'bg-warning text-dark',
            self::STATUS_IN_PROGRESS => 'bg-info text-white',
            self::STATUS_FINALIZED   => 'bg-success',
            default                  => 'bg-secondary',
        };
    }

    // ── Legacy helpers (kept for compatibility) ───────────────────

    public function isIntakeOnly(): bool
    {
        return $this->recorded_by === 'secretary' && $this->isPending();
    }

    // ── Relationships ─────────────────────────────────────────────

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function finalizedBy()
    {
        return $this->belongsTo(User::class, 'finalized_by');
    }
}