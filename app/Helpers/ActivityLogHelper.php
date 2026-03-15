<?php

namespace App\Helpers;

class ActivityLogHelper
{
    public static function actionLabel(string $action): string
    {
        return match ($action) {
            'auth.login' => 'logged in',
            'auth.logout' => 'logged out',
            'auth.login_failed' => 'FAILED LOGIN attempt',
            'patient.created' => 'added new patient',
            'patient.viewed' => 'viewed patient record',
            'patient.updated' => 'updated patient record',
            'appointment.booked' => 'booked appointment for',
            'appointment.confirmed' => 'confirmed appointment for',
            'appointment.completed' => 'completed appointment for',
            'appointment.cancelled' => 'cancelled appointment for',
            'visit.recorded' => 'recorded visit for',
            'visit.intake_recorded' => 'recorded intake for',
            'visit.updated' => 'updated visit for',
            'user.created' => 'created account for',
            'user.updated' => 'updated account for',
            default => str_replace(['.', '_'], ' ', $action),
        };
    }
}