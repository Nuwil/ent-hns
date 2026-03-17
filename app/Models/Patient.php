<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'first_name', 'last_name', 'date_of_birth', 'gender',
        'phone', 'occupation',
        'province', 'city', 'address',
        'blood_type', 'allergies',
        'insurance_info', 'medical_history',
        'notes',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    // ── Accessors ─────────────────────────────────────────────────

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getAgeAttribute(): int
    {
        return $this->date_of_birth->age;
    }

    public function getFullAddressAttribute(): string
    {
        return collect([$this->address, $this->city, $this->province])
            ->filter()->implode(', ');
    }

    // ── Relationships ─────────────────────────────────────────────

    public function appointments()
    {
        return $this->hasMany(Appointment::class)->latest();
    }

    public function visits()
    {
        return $this->hasMany(Visit::class)->latest();
    }

    public function latestVisit()
    {
        return $this->hasOne(Visit::class)->latestOfMany();
    }
}