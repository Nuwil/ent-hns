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
        'email',
        'password_hash',
        'full_name',
        'role',
        'is_active',
        'is_protected',
    ];

    protected $hidden = [
        'password_hash',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_protected' => 'boolean',
    ];

    /**
     * Boot the model and set up event listeners.
     *
     * Auto-protect the very first administrator account regardless of how it
     * is created.  This ensures the special user cannot be deleted or
     * deactivated later on.
     */
    protected static function booted()
    {
        static::creating(function ($user) {
            if ($user->role === 'admin' && User::where('role', 'admin')->count() === 0) {
                $user->is_protected = true;
            }
        });
    }

    // Override getAuthPassword to use password_hash instead of password
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'doctor_id');
    }

    public function visits()
    {
        return $this->hasMany(PatientVisit::class, 'doctor_id');
    }

    public function recordings()
    {
        return $this->hasMany(Recording::class, 'recorded_by');
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }
}