<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrescriptionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'visit_id',
        'patient_id',
        'medicine_id',
        'medicine_name',
        'instruction',
        'doctor_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public $timestamps = false;

    public function visit()
    {
        return $this->belongsTo(PatientVisit::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }
}
