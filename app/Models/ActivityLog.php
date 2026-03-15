<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'user_name',
        'user_role',
        'action',
        'description',
        'subject_type',
        'subject_id',
        'subject_label',
        'severity',
        'ip_address',
        'user_agent',
    ];

    // ── Relationships ─────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── Static logger — call this from anywhere ───────────────────

    /**
     * Log an activity.
     *
     * @param string      $action       e.g. 'patient.viewed'
     * @param string      $description  Human-readable e.g. 'Viewed patient John Doe'
     * @param string      $severity     'info' | 'warning' | 'danger'
     * @param Model|null  $subject      The model being acted on (Patient, Appointment, etc.)
     */
    public static function log(
        string $action,
        string $description,
        string $severity = 'info',
        ?Model $subject = null,
        ?User $actor = null
    ): self {
        $user = $actor ?? (Auth::check() ? Auth::user() : null);

        return self::create([
            'user_id'       => $user?->id,
            'user_name'     => $user?->name ?? 'System',
            'user_role'     => $user?->role ?? null,
            'action'        => $action,
            'description'   => $description,
            'severity'      => $severity,
            'subject_type'  => $subject ? class_basename($subject) : null,
            'subject_id'    => $subject?->id,
            'subject_label' => self::subjectLabel($subject),
            'ip_address'    => Request::ip(),
            'user_agent'    => substr(Request::userAgent() ?? '', 0, 200),
        ]);
    }

    // ── Helpers ───────────────────────────────────────────────────

    private static function subjectLabel(?Model $subject): ?string
    {
        if (!$subject) return null;

        return match (true) {
            $subject instanceof Patient     => $subject->full_name,
            $subject instanceof Appointment => "Appointment #{$subject->id}",
            $subject instanceof User        => $subject->name,
            $subject instanceof Visit       => "Visit #{$subject->id}",
            default                         => class_basename($subject) . " #{$subject->id}",
        };
    }

    public function iconClass(): string
    {
        return match (true) {
            str_starts_with($this->action, 'auth.')        => 'bi-shield-lock',
            str_starts_with($this->action, 'patient.')     => 'bi-person',
            str_starts_with($this->action, 'appointment.') => 'bi-calendar2',
            str_starts_with($this->action, 'visit.')       => 'bi-clipboard2-pulse',
            str_starts_with($this->action, 'user.')        => 'bi-person-gear',
            default                                         => 'bi-activity',
        };
    }

    public function severityColorClass(): string
    {
        return match ($this->severity) {
            'warning' => 'log-warning',
            'danger'  => 'log-danger',
            default   => 'log-info',
        };
    }
}