<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

/**
 * Visit
 *
 * The core clinical record: one row per patient consultation. A visit is
 * typically created in two stages —
 *
 *   1. Secretary intake: chief complaint, vitals (blood pressure/weight/
 *      height), ENT classification. Status starts at STATUS_PENDING.
 *   2. Doctor consultation: history, physical exam, diagnosis, treatment
 *      plan, and prescriptions are added. Status moves to
 *      STATUS_IN_PROGRESS while the doctor is working, then
 *      STATUS_FINALIZED once they sign off.
 *
 * A visit may optionally be linked to the Appointment it originated from
 * via appointment_id (see the appointment() relationship below) — this
 * makes it possible to show the original appointment date even if the
 * actual consultation happened on a different day.
 *
 * Retention policy: visits are archived after ~1 year (archived_at set,
 * hidden from the main timeline but still in the DB) and soft-deleted
 * after ~2 years (see App\Console\Commands\ArchiveOldVisits, scheduled
 * daily in routes/console.php). SoftDeletes means "deleted" visits are
 * recoverable, not gone.
 */
class Visit extends Model
{
    use HasFactory, SoftDeletes;

    // Workflow status constants — always compare against these, not raw strings.
    const STATUS_PENDING     = 'pending';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_FINALIZED   = 'finalized';

    protected $fillable = [
        'patient_id', 'doctor_id', 'appointment_id', 'visited_at',
        // Secretary intake fields
        'chief_complaint', 'ent_classification',
        'blood_pressure', 'weight', 'height',
        // Doctor consultation fields
        'history', 'history_of_illness',
        'physical_exam', 'exam_findings',
        'diagnosis', 'treatment_plan',
        'plan_instructions',
        'notes', 'prescriptions', 'follow_up_date',
        // Workflow bookkeeping
        'recorded_by', 'status', 'finalized_by', 'finalized_at',
        'archived_at',
    ];

    protected $casts = [
        'visited_at'     => 'datetime',
        'finalized_at'   => 'datetime',
        'archived_at'    => 'datetime',
        'follow_up_date' => 'date',
        'prescriptions'  => 'array', // stored as JSON: [{drug, dosage, quantity}, ...]
    ];

    // ── Retention scopes/helpers ───────────────────────────────────
    // See App\Console\Commands\ArchiveOldVisits for what actually sets archived_at.

    /** Visits still shown on the main patient timeline (not yet archived). */
    public function scopeActive($query)   { return $query->whereNull('archived_at'); }

    /** Visits older than ~1 year, hidden from the main timeline but still in the DB. */
    public function scopeArchived($query) { return $query->whereNotNull('archived_at'); }

    public function isArchived(): bool    { return $this->archived_at !== null; }

    // ── Status helpers ────────────────────────────────────────────

    public function isPending(): bool    { return $this->status === self::STATUS_PENDING; }
    public function isInProgress(): bool { return $this->status === self::STATUS_IN_PROGRESS; }
    public function isFinalized(): bool  { return $this->status === self::STATUS_FINALIZED; }

    /** True when a secretary created the visit but a doctor hasn't touched it yet. */
    public function isIntakeOnly(): bool
    {
        return $this->recorded_by === 'secretary'
            && $this->status === self::STATUS_PENDING;
    }

    /** True while the visit is still awaiting a doctor — used to lock intake fields from further secretary edits mid-consultation. */
    public function isLocked(): bool { return $this->status === self::STATUS_PENDING; }

    /** A secretary may edit their own intake right up until the doctor finalizes the visit. */
    public function secretaryCanEdit(): bool
    {
        return $this->recorded_by === 'secretary'
            && in_array($this->status, [self::STATUS_PENDING, self::STATUS_IN_PROGRESS]);
    }

    /** Only the assigned doctor may edit clinical fields, and only before finalization. */
    public function doctorCanEdit(User $user): bool
    {
        return $this->doctor_id === $user->id
            && in_array($this->status, [self::STATUS_PENDING, self::STATUS_IN_PROGRESS]);
    }

    /** Human-readable status for display (Blade views, exports). */
    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING     => 'Awaiting Doctor',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_FINALIZED   => 'Finalized',
            default                  => ucfirst($this->status),
        };
    }

    /** Bootstrap badge class matching the current status. */
    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING     => 'bg-warning text-dark',
            self::STATUS_IN_PROGRESS => 'bg-info text-white',
            self::STATUS_FINALIZED   => 'bg-success',
            default                  => 'bg-secondary',
        };
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

    /** The appointment this visit was booked from, if any (nullable — many visits are created manually). */
    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function finalizedBy()
    {
        return $this->belongsTo(User::class, 'finalized_by');
    }
}
