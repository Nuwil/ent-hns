<?php
require 'vendor/autoload.php';
$app = include 'bootstrap/app.php';

$db = $app->make('Illuminate\Database\DatabaseManager');

$sessions = $db->table('sessions')->latest('last_activity')->first();

if ($sessions) {
    echo "=== Latest Session ===\n";
    echo "Session ID: " . substr($sessions->id, 0, 20) . "...\n";
    echo "User ID Column: " . ($sessions->user_id ?? 'NULL') . "\n";
    echo "Last Activity: " . $sessions->last_activity . "\n";
    echo "\n=== Decoded Payload ===\n";
    
    $payload = unserialize(base64_decode($sessions->payload));
    echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
} else {
    echo "No sessions in database\n";
}
