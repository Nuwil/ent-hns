<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'email',
        'full_name',
        'phone',
        'occupation',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'medical_history',
        'current_medications',
        'allergies',
        'vaccine_history',
        'insurance_provider',
        'insurance_id',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
        'height',
        'weight',
        'bmi',
        'blood_pressure',
        'temperature',
        'vitals_updated_at',
        'created_by',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'vitals_updated_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function visits()
    {
        return $this->hasMany(PatientVisit::class);
    }

    public function recordings()
    {
        return $this->hasMany(Recording::class);
    }

    public function prescriptions()
    {
        return $this->hasMany(PrescriptionItem::class);
    }

    public function waitlistEntry()
    {
        return $this->hasOne(Waitlist::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
