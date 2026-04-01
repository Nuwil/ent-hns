<?php

namespace App\Helpers;

use App\Models\Notification;
use App\Models\User;

class NotificationHelper
{
    /**
     * Send a notification to a specific user.
     */
    public static function send(
        int    $userId,
        string $type,
        string $title,
        string $message,
        string $url   = '',
        string $icon  = 'bi-bell',
        string $color = 'primary'
    ): void {
        // Don't send to self
        if ($userId === auth()->id()) return;

        Notification::create([
            'user_id' => $userId,
            'type'    => $type,
            'title'   => $title,
            'message' => $message,
            'url'     => $url,
            'icon'    => $icon,
            'color'   => $color,
        ]);
    }

    /**
     * Notify all doctors with a specific role.
     */
    public static function sendToRole(
        string $role,
        string $type,
        string $title,
        string $message,
        string $url   = '',
        string $icon  = 'bi-bell',
        string $color = 'primary'
    ): void {
        $users = User::where('role', $role)->where('is_active', true)->get();
        foreach ($users as $user) {
            self::send($user->id, $type, $title, $message, $url, $icon, $color);
        }
    }

    // ── Specific Notification Senders ─────────────────────────

    /**
     * Secretary booked an appointment → notify assigned doctor
     */
    public static function appointmentBooked(int $doctorId, string $patientName, string $date, int $appointmentId): void
    {
        self::send(
            userId:  $doctorId,
            type:    'appointment.booked',
            title:   'New Appointment Booked',
            message: "Secretary booked an appointment for {$patientName} on {$date}.",
            url:     "/doctor/appointments",
            icon:    'bi-calendar-plus-fill',
            color:   'primary'
        );
    }

    /**
     * Secretary created intake visit → notify assigned doctor
     */
    public static function intakeCreated(int $doctorId, string $patientName, string $complaint, int $patientId): void
    {
        self::send(
            userId:  $doctorId,
            type:    'visit.intake',
            title:   'New Patient Intake',
            message: "{$patientName} is awaiting your consultation. CC: {$complaint}.",
            url:     "/doctor/patients/{$patientId}",
            icon:    'bi-clipboard2-pulse-fill',
            color:   'warning'
        );
    }

    /**
     * Doctor confirmed appointment → notify all secretaries
     */
    public static function appointmentConfirmed(string $doctorName, string $patientName, int $appointmentId): void
    {
        self::sendToRole(
            role:    'secretary',
            type:    'appointment.confirmed',
            title:   'Appointment Confirmed',
            message: "Dr. {$doctorName} confirmed the appointment for {$patientName}.",
            url:     "/secretary/appointments",
            icon:    'bi-calendar-check-fill',
            color:   'success'
        );
    }

    /**
     * Doctor cancelled appointment → notify all secretaries
     */
    public static function appointmentCancelled(string $doctorName, string $patientName, int $appointmentId): void
    {
        self::sendToRole(
            role:    'secretary',
            type:    'appointment.cancelled',
            title:   'Appointment Cancelled',
            message: "Dr. {$doctorName} cancelled the appointment for {$patientName}.",
            url:     "/secretary/appointments",
            icon:    'bi-calendar-x-fill',
            color:   'danger'
        );
    }

    /**
     * Doctor finalized visit → notify all secretaries
     */
    public static function visitFinalized(string $doctorName, string $patientName, int $patientId): void
    {
        self::sendToRole(
            role:    'secretary',
            type:    'visit.finalized',
            title:   'Visit Finalized',
            message: "Dr. {$doctorName} finalized the visit record for {$patientName}.",
            url:     "/secretary/patients/{$patientId}",
            icon:    'bi-lock-fill',
            color:   'success'
        );
    }
}