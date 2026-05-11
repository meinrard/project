<?php
include("db_connect.php");
mysqli_set_charset($conn, "utf8");

header('Content-Type: application/json; charset=utf-8');

$response = [
    'success' => false,
    'message' => '',
    'fingerprint_id' => null,
    'student_id' => null,
    'student_name' => null,
    'status' => null
];

$fingerprint_id = null;
if (isset($_REQUEST['fingerprint_id']) && is_numeric($_REQUEST['fingerprint_id'])) {
    $fingerprint_id = intval($_REQUEST['fingerprint_id']);
}

if (!$fingerprint_id) {
    http_response_code(400);
    $response['message'] = 'Invalid or missing fingerprint_id. Please provide a valid numeric fingerprint ID.';
    echo json_encode($response);
    exit;
}

$response['fingerprint_id'] = $fingerprint_id;

$stmt = mysqli_prepare($conn, "SELECT student_id, student_name FROM students WHERE fingerprint_id = ?");
if (!$stmt) {
    http_response_code(500);
    $response['message'] = 'Database error preparing query.';
    echo json_encode($response);
    exit;
}

mysqli_stmt_bind_param($stmt, "i", $fingerprint_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    $student_id = $row['student_id'];
    $student_name = $row['student_name'];
    $response['student_id'] = $student_id;
    $response['student_name'] = $student_name;

    $todayCheck = mysqli_prepare($conn, "SELECT attendance_id, status FROM attendance WHERE student_id = ? AND DATE(time_in) = CURRENT_DATE()");
    if (!$todayCheck) {
        http_response_code(500);
        $response['message'] = 'Database error preparing attendance check.';
        echo json_encode($response);
        exit;
    }

    mysqli_stmt_bind_param($todayCheck, "i", $student_id);
    mysqli_stmt_execute($todayCheck);
    mysqli_stmt_store_result($todayCheck);

    if (mysqli_stmt_num_rows($todayCheck) > 0) {
        http_response_code(200);
        $response['success'] = true;
        $response['status'] = 'already_recorded';
        $response['message'] = "Verified: $student_name (Fingerprint ID $fingerprint_id) - Attendance already recorded today.";
        echo json_encode($response);
        exit;
    }

    $attendanceStmt = mysqli_prepare($conn, "INSERT INTO attendance (student_id, status) VALUES (?, 'Present')");
    if (!$attendanceStmt) {
        http_response_code(500);
        $response['message'] = 'Database error preparing attendance insert.';
        echo json_encode($response);
        exit;
    }

    mysqli_stmt_bind_param($attendanceStmt, "i", $student_id);

    if (mysqli_stmt_execute($attendanceStmt)) {
        http_response_code(200);
        $response['success'] = true;
        $response['status'] = 'recorded';
        $response['message'] = "Verified: $student_name (Fingerprint ID $fingerprint_id) - Attendance recorded.";
    } else {
        http_response_code(500);
        $response['message'] = 'Error recording attendance. Please try again.';
    }
} else {
    http_response_code(404);
    $response['message'] = 'Fingerprint not recognized. Student not registered or wrong finger placed on the scanner.';
}

echo json_encode($response);
