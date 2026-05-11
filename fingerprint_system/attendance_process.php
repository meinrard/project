<?php
include("db_connect.php");
mysqli_set_charset($conn, "utf8");
header('Content-Type: text/plain; charset=utf-8');

function getScannedFingerprintId() {
    if (isset($_REQUEST['fingerprint_id']) && is_numeric($_REQUEST['fingerprint_id'])) {
        return intval($_REQUEST['fingerprint_id']);
    }

    // The fingerprint ID can come from the browser serial connection to the Arduino.
    return null;
}

$fingerprint_id = getScannedFingerprintId();

if (!$fingerprint_id) {
    http_response_code(400);
    echo "ERROR: Could not access fingerprint scanner. Please make sure the R305 scanner is connected and the capture service is configured.";
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT student_id, student_name FROM students WHERE fingerprint_id = ?");
mysqli_stmt_bind_param($stmt, "i", $fingerprint_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    $student_id = $row['student_id'];
    $student_name = $row['student_name'];

    $todayCheck = mysqli_prepare($conn, "SELECT attendance_id FROM attendance WHERE student_id = ? AND DATE(time_in) = CURRENT_DATE()");
    mysqli_stmt_bind_param($todayCheck, "i", $student_id);
    mysqli_stmt_execute($todayCheck);
    mysqli_stmt_store_result($todayCheck);

    if (mysqli_stmt_num_rows($todayCheck) > 0) {
        echo "Verified: $student_name (Fingerprint ID $fingerprint_id) - Attendance already recorded today.";
        exit;
    }

    $attendanceStmt = mysqli_prepare($conn, "INSERT INTO attendance (student_id, status) VALUES (?, 'Present')");
    mysqli_stmt_bind_param($attendanceStmt, "i", $student_id);

    if (mysqli_stmt_execute($attendanceStmt)) {
        echo "Verified: $student_name (Fingerprint ID $fingerprint_id) - Attendance recorded.";
    } else {
        echo "Error recording attendance. Please try again.";
    }
} else {
    echo "Fingerprint not recognized. Student not registered or wrong finger placed in scan.";
}
