<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Analytics extends Model
{
    use HasFactory;

    protected $fillable = [
        'metric_type',
        'metric_name',
        'metric_value',
        'doctor_id',
        'measurement_date',
        'additional_data',
    ];

    protected $casts = [
        'measurement_date' => 'date',
        'additional_data' => 'json',
        'created_at' => 'datetime',
    ];

    public $timestamps = false;
}
