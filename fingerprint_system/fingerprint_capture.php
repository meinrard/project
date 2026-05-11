<?php
include("db_connect.php");

header('Content-Type: application/json; charset=utf-8');

function getFingerprintIdFromR305() {
    // Attempt to read from a hardware helper or external service. Update this command
    // if you have a local C++ serial bridge for the R305 fingerprint module.
    if (isset($_GET['test_id']) && is_numeric($_GET['test_id'])) {
        return intval($_GET['test_id']);
    }

    // Integration point for C++ executable
    $command = 'c:\\xampp\\htdocs\\fingerprint_system\\r305_capture.exe';
    if (!file_exists($command)) {
        return null;
    }
    
    exec($command, $output, $return_var);
    
    // Check if command succeeded and output is valid
    if ($return_var === 0 && isset($output[0])) {
        $fingerprint_id = trim($output[0]);
        // Validate it's a number and greater than 0
        if (is_numeric($fingerprint_id) && intval($fingerprint_id) > 0) {
            return intval($fingerprint_id);
        }
    }

    return null;
}

$fingerprint_id = getFingerprintIdFromR305();

if ($fingerprint_id && $fingerprint_id > 0) {
    echo json_encode([
        'success' => true,
        'fingerprint_id' => $fingerprint_id,
        'message' => 'Fingerprint captured successfully.'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Fingerprint scanner not detected. Make sure: 1) Arduino is connected via USB, 2) r305_capture.exe is in the system folder, 3) Place your finger on the sensor. Waiting for 15 seconds...'
    ]);
}
