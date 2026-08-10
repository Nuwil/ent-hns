<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Patient
 *
 * Represents a person registered at the clinic. A patient may have many
 * Appointments (future/past bookings) and many Visits (actual recorded
 * consultations, which may or may not have originated from an appointment
 * — see Visit::cameFromAcceptedAppointment()).
 *
 * Uses SoftDeletes so that removing a patient from the active roster
 * doesn't destroy their historical medical records; they can be restored.
 *
 * @property string $first_name
 * @property string $last_name
 * @property \Illuminate\Support\Carbon $date_of_birth
 * @property string $gender
 */
class Patient extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'first_name', 'last_name', 'date_of_birth', 'gender', 'religion',
        'phone', 'occupation',
        'province', 'city', 'address',
        'blood_type', 'allergies',
        'insurance_info', 'medical_history', 'vaccine_history',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    // ── Accessors ─────────────────────────────────────────────────
    // These let Blade/PHP read $patient->full_name, ->age, and
    // ->full_address as if they were real columns.

    /** Combines first + last name for display (e.g. table rows, headers). */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /** Current age in years, computed from date_of_birth. */
    public function getAgeAttribute(): int
    {
        return $this->date_of_birth->age;
    }

    /** Comma-joined address (street, city, province), skipping any empty parts. */
    public function getFullAddressAttribute(): string
    {
        return collect([$this->address, $this->city, $this->province])
            ->filter()->implode(', ');
    }

    // ── Relationships ─────────────────────────────────────────────

    /** All appointments ever booked for this patient, most recent first. */
    public function appointments()
    {
        return $this->hasMany(Appointment::class)->latest();
    }

    /** All recorded visits/consultations for this patient, most recent first. */
    public function visits()
    {
        return $this->hasMany(Visit::class)->latest();
    }

    /** Just the single most recent visit — used for dashboard "last seen" style displays. */
    public function latestVisit()
    {
        return $this->hasOne(Visit::class)->latestOfMany();
    }
}