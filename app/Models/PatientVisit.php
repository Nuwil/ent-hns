<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientVisit extends Model
{
    use HasFactory;

    protected $table = 'patient_visits';

    protected $fillable = [
        'patient_id',
        'appointment_id',
        'visit_date',
        'visit_type',
        'ent_type',
        'chief_complaint',
        'history',
        'physical_exam',
        'diagnosis',
        'treatment_plan',
        'prescription',
        'notes',
        'height',
        'weight',
        'blood_pressure',
        'temperature',
        'pulse_rate',
        'respiratory_rate',
        'oxygen_saturation',
        'vitals_notes',
        'doctor_id',
        'doctor_name',
    ];

    protected $casts = [
        'visit_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function prescriptions()
    {
        return $this->hasMany(PrescriptionItem::class, 'visit_id');
    }
}
