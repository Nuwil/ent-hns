<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'full_name',
        'email',
        'password_hash',
        'role',
        'is_active',
        'is_protected',
        'is_head_doctor',
    ];

    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    protected $casts = [
        'is_active'       => 'boolean',
        'is_head_doctor'  => 'boolean',
    ];

    /**
     * Tell Laravel which column stores the password.
     */
    public function getAuthPassword(): string
    {
        return $this->password_hash;
    }

    // ── Role helpers ──────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isSecretary(): bool
    {
        return $this->role === 'secretary';
    }

    public function isDoctor(): bool
    {
        return $this->role === 'doctor';
    }

    public function isHeadDoctor(): bool
    {
        return $this->role === 'doctor' && $this->is_head_doctor;
    }

    /**
     * Head doctors can see all patient records; regular doctors only their own.
     */
    public function canViewAllPatients(): bool
    {
        return in_array($this->role, ['admin', 'secretary']) || $this->is_head_doctor;
    }

    /**
     * Can this user access a specific patient's timeline/show page?
     *
     * Rules:
     *  - Admins, secretaries, head doctors → always yes
     *  - Regular doctors → only if they:
     *      (a) created the patient record, OR
     *      (b) are assigned as doctor on at least one appointment, OR
     *      (c) have at least one visit with that patient
     */
    public function canAccessPatient(\App\Models\Patient $patient): bool
    {
        if ($this->canViewAllPatients()) {
            return true;
        }

        if ($this->isDoctor()) {
            // Created the record
            if ((int) $patient->created_by === $this->id) return true;

            // Assigned on an appointment
            if ($patient->appointments()->where('doctor_id', $this->id)->exists()) return true;

            // Has a visit with this patient
            if ($patient->visits()->where('doctor_id', $this->id)->exists()) return true;
        }

        return false;
    }

    public function dashboardRoute(): string
    {
        return match ($this->role) {
            'admin'     => 'admin.dashboard',
            'secretary' => 'secretary.dashboard',
            'doctor'    => 'doctor.dashboard',
            default     => 'login',
        };
    }

    // ── Accessor: map full_name → name for blade templates ───────
    public function getNameAttribute(): string
    {
        return $this->full_name ?? $this->username;
    }

    // ── Relationships ─────────────────────────────────────────────

    public function visits()
    {
        return $this->hasMany(Visit::class, 'doctor_id');
    }
}