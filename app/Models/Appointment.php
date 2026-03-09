<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'appointment_date',
        'appointment_type',
        'duration',
        'status',
        'notes',
        'blood_pressure',
        'temperature',
        'pulse_rate',
        'respiratory_rate',
        'oxygen_saturation',
        'rescheduled_from',
        'rescheduled_to',
        'cancellation_reason',
    ];

    protected $casts = [
        'appointment_date' => 'datetime',
        'rescheduled_from' => 'datetime',
        'rescheduled_to' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function visits()
    {
        return $this->hasMany(PatientVisit::class);
    }
}
