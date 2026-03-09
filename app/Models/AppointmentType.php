<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppointmentType extends Model
{
    use HasFactory;

    protected $table = 'appointment_types';

    protected $primaryKey = 'key';
    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'key',
        'label',
        'duration_minutes',
        'buffer_minutes',
        'daily_max',
    ];
}
