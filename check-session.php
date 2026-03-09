<?php

require 'vendor/autoload.php';
$app = include 'bootstrap/app.php';

// Get latest session
$session = \DB::table('sessions')->latest('last_activity')->first();

if ($session) {
    echo "Session ID: " . $session->id . "\n";
    echo "User ID in DB column: " . ($session->user_id ?? 'NULL') . "\n";
    echo "Last Activity: " . $session->last_activity . "\n";
    echo "\nPayload decoded:\n";
    $payload = unserialize(base64_decode($session->payload));
    echo json_encode($payload, JSON_PRETTY_PRINT);
} else {
    echo "No sessions found\n";
}
