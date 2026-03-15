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
    ];

    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
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