<?php
session_start();
include("db_connect.php");

// Check login
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Save student
if (isset($_POST['register'])) {

    $student_name = mysqli_real_escape_string($conn, trim($_POST['student_name']));
    $reg_number = mysqli_real_escape_string($conn, trim($_POST['reg_number']));
    $course = mysqli_real_escape_string($conn, trim($_POST['course']));
    $semester = intval($_POST['semester']);
    $fingerprint_id = intval($_POST['fingerprint_id']);

    if ($fingerprint_id <= 0) {
        echo "<script>alert('Please capture a valid fingerprint ID from the scanner before registering.');</script>";
    } else {
        $query = "INSERT INTO students (student_name, reg_number, course, semester, fingerprint_id) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sssii", $student_name, $reg_number, $course, $semester, $fingerprint_id);

        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Student Registered Successfully');</script>";
        } else {
            $error = mysqli_error($conn);
            echo "<script>alert('Error Registering Student: " . addslashes($error) . "');</script>";
        }
    }

}
?>

<!DOCTYPE html>
<html>
<head>

<title>Student Enrollment</title>

<style>

body {
    margin: 0;
    font-family: Arial;
    background-color: #f2f2f2;
    padding-bottom: 160px;
}

/* HEADER */
.header {
    background: #002147;
    color: white;
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.left-header {
    display: flex;
    align-items: center;
}

.header h2 {
    margin: 0;
    font-size: 18px;
    margin-left: 10px;
}

.logo {
    height: 45px;
}

.menu-icon {
    font-size: 28px;
    cursor: pointer;
}

/* SIDE MENU */
.side-menu {
    height: 100%;
    width: 0;
    position: fixed;
    top: 0;
    right: 0;
    background: #003366;
    overflow-x: hidden;
    transition: 0.3s;
    padding-top: 60px;
    z-index: 999;
}

.side-menu a {
    padding: 15px;
    display: block;
    color: white;
    text-decoration: none;
}

.side-menu a .link-icon {
    display: inline-block;
    width: 24px;
    text-align: center;
    margin-right: 10px;
}

.side-menu a:hover {
    background: #012952;
}

.close-btn {
    position: absolute;
    top: 10px;
    right: 20px;
    font-size: 30px;
    color: white;
    cursor: pointer;
}

.content {
    padding: 40px;
}

.container {
    max-width: 650px;
    margin: 0 auto;
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 0 12px rgba(0, 0, 0, 0.12);
}

.container h2 {
    margin-top: 0;
}

input, select, button {
    width: 100%;
    padding: 12px;
    margin: 8px 0;
    border-radius: 6px;
    border: 1px solid #ccc;
    box-sizing: border-box;
}

input, select {
    background: #fafafa;
}

.fingerprint-row {
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 10px;
    align-items: center;
}

.fingerprint-status {
    margin: 0 0 8px 0;
    color: #333;
    font-size: 0.95rem;
}

button {
    background-color: #002147;
    color: white;
    border: none;
    cursor: pointer;
}

button.capture {
    width: auto;
    padding: 12px 18px;
    background-color: #0056a6;
}

button:hover {
    opacity: 0.95;
}

.back {
    background-color: gray;
}

.whatsapp-support {
    margin: 0 40px 20px;
    display: flex;
    gap: 10px;
}

.icon-button,
.admin-assist-link {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 42px;
    height: 42px;
    border-radius: 50%;
    color: #fff;
    text-decoration: none;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

.whatsapp-support a {
    background: #25D366;
}

.admin-assist-link {
    background: #4B9AFF;
}

.icon-button:hover,
.admin-assist-link:hover {
    opacity: 0.92;
}

.icon-label {
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translate(-50%, 8px);
    background: rgba(0, 0, 0, 0.85);
    color: #fff;
    padding: 4px 8px;
    border-radius: 4px;
    white-space: nowrap;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.15s ease, transform 0.15s ease;
    font-size: 11px;
    z-index: 2;
}

.icon-button:hover .icon-label,
.admin-assist-link:hover .icon-label {
    opacity: 1;
    transform: translate(-50%, 0);
}

.whatsapp-icon,
.admin-icon {
    width: 22px;
    height: 22px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.whatsapp-icon svg,
.admin-icon svg {
    width: 100%;
    height: 100%;
    display: block;
}

/* FOOTER */
.footer {
    background: #002147;
    color: white;
    padding: 15px 20px;
    position: fixed;
    bottom: 0;
    width: 100%;
    display: flex;
    flex-wrap: wrap;
    align-items: flex-start;
    justify-content: flex-start;
    gap: 15px;
    font-size: 13px;
}

.footer img {
    height: 30px;
    flex-shrink: 0;
}

.footer-content {
    flex: 1 1 320px;
    line-height: 1.4;
    min-width: 220px;
}

.footer-line {
    margin-bottom: 4px;
    white-space: normal;
    overflow-wrap: anywhere;
}

.footer-meta {
    flex: 1 1 320px;
    min-width: 220px;
    line-height: 1.4;
    display: flex;
    flex-direction: column;
    gap: 4px;
    text-align: left;
}

.footer-meta div {
    white-space: normal;
    overflow-wrap: anywhere;
}

@media (max-width: 800px) {
    .footer {
        flex-direction: column;
        align-items: flex-start;
        text-align: left;
    }

    .footer-meta {
        text-align: left;
    }
}

</style>

</head>

<body>

<!-- HEADER -->
<div class="header">
    <div class="left-header">
        <img src="logo.png" class="logo" alt="RUCU Logo">
        <h2>RUAHA CATHOLIC UNIVERSITY (RUCU)</h2>
    </div>
    <span class="menu-icon" onclick="openMenu()">☰</span>
</div>

<!-- SIDE MENU -->
<div id="sideMenu" class="side-menu">
    <span class="close-btn" onclick="closeMenu()">×</span>
    <a href="student_enrollment.php"><span class="link-icon">🎓</span>Student Enrollment</a>
    <a href="fees_payment.php"><span class="link-icon">💳</span>Fees Payment</a>
    <a href="create_exam.php"><span class="link-icon">📝</span>Create Examination</a>
    <a href="verification_attendance.php"><span class="link-icon">✔️</span>Verification & Attendance</a>
    <a href="logout.php"><span class="link-icon">🚪</span>Logout</a>
</div>

<div class="content">
    <div class="container">
        <h2>Student Enrollment</h2>

        <form method="POST">
            <input type="text" name="student_name" placeholder="Student Name" required>
            <input type="text" name="reg_number" placeholder="Registration Number" required>
            <input type="text" name="course" placeholder="Course (Example: BSc Computer Science)" required>
            <select name="semester" required>
                <option value="">Select Semester</option>
                <option value="1">Semester 1</option>
                <option value="2">Semester 2</option>
            </select>
            <div class="fingerprint-row">
                <input type="text" id="fingerprint_id" name="fingerprint_id" placeholder="Fingerprint ID will appear here" readonly required>
                <button type="button" class="capture" onclick="captureFingerprint()">Capture from R305</button>
            </div>
            <div id="fingerprintStatus" class="fingerprint-status">Fingerprint status: waiting</div>
            <button type="submit" name="register">Register Student</button>
        </form>

        <br>

        <form action="dashboard.php">
            <button class="back">Back to Dashboard</button>
        </form>
    </div>
</div>

<div class="whatsapp-support">
    <a href="https://wa.me/255765169041?text=Hello%20RUCU%20Admin%2C%20I%20need%20help%20with%20the%20dashboard." class="icon-button" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp Help">
        <span class="whatsapp-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="#ffffff">
                <path d="M20.52 3.48A11.93 11.93 0 0012 0C5.37 0 0 5.37 0 12c0 2.13.56 4.12 1.54 5.84L0 24l6.45-1.68A11.95 11.95 0 0012 24c6.63 0 12-5.37 12-12 0-3.2-1.25-6.2-3.48-8.52zM12 21.52c-1.88 0-3.68-.52-5.23-1.5l-.37-.22-3.84 1.0 1.04-3.74-.24-.38A9.45 9.45 0 012.5 12c0-5.25 4.25-9.5 9.5-9.5 5.25 0 9.5 4.25 9.5 9.5S17.25 21.52 12 21.52zm4.57-6.82c-.18-.09-1.08-.53-1.25-.59-.17-.06-.29-.09-.41.09s-.47.59-.58.72c-.11.12-.22.14-.4.05-.18-.09-.75-.28-1.42-.88-.52-.46-.87-1.03-.97-1.22-.1-.19-.01-.29.08-.38.08-.08.18-.19.27-.29.09-.1.12-.18.18-.29.06-.11.03-.21-.01-.29-.05-.08-.41-1-0.56-1.38-.15-.38-.31-.33-.41-.34-.11-.01-.24-.01-.37-.01-.13 0-.34.05-.52.24-.18.19-.69.68-.69 1.66 0 .98.71 1.93.81 2.06.1.13 1.39 2.21 3.38 3.1.47.2.84.32 1.13.41.47.15.9.13 1.24.08.38-.05 1.08-.44 1.24-.86.16-.42.16-.78.11-.86-.05-.08-.17-.13-.36-.22z"/>
            </svg>
        </span>
        <span class="icon-label">WhatsApp</span>
    </a>
    <a href="#" class="admin-assist-link" onclick="openAdminAssistance(event)" aria-label="Admin Assistance">
        <span class="admin-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="#ffffff">
                <path d="M12 2a7 7 0 017 7v2a7 7 0 01-7 7 7 7 0 01-7-7V9a7 7 0 017-7zm0 2a5 5 0 00-5 5v2a5 5 0 0010 0V9a5 5 0 00-5-5zm-7 14h14v2H5v-2zm7 4l4-4H8l4 4z"/>
            </svg>
        </span>
        <span class="icon-label">Admin Assistance</span>
    </a>
</div>

<!-- FOOTER -->
<div class="footer">
    <img src="logo.png" alt="RUCU Logo">
    <div class="footer-content">
        <div class="footer-line">P.O. BOX 774, IRINGA | rucu@rucu.ac.tz</div>
        <div class="footer-line">FACEBOOK, TWITTER, INSTAGRAM, LINKEDIN</div>
        <div class="footer-line">Developed by ICT (RUCU) UNIT</div>
    </div>
    <div class="footer-meta">
        <div id="liveDateTime"></div>
        <div>Admin Login: Authorized Users Only | RUCU Fingerprint System</div>
    </div>
</div>

<script>
function openMenu() {
    document.getElementById("sideMenu").style.width = "250px";
}

function closeMenu() {
    document.getElementById("sideMenu").style.width = "0";
}

function openAdminAssistance(event) {
    event.preventDefault();
    alert('Admin assistance (AI) is coming soon. Please contact the RUCU ICT unit for support.');
}

function captureFingerprint() {
    const status = document.getElementById('fingerprintStatus');
    status.textContent = 'Waiting for the fingerprint sensor...';

    fetch('fingerprint_capture.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('fingerprint_id').value = data.fingerprint_id;
                status.textContent = 'Captured fingerprint ID: ' + data.fingerprint_id;
            } else {
                status.textContent = 'Capture failed: ' + data.message;
                alert('Fingerprint capture failed: ' + data.message);
            }
        })
        .catch(error => {
            status.textContent = 'Capture failed: ' + error.message;
            alert('Fingerprint capture failed. Check your scanner setup.');
        });
}

function updateDateTime() {
    const now = new Date();
    const options = {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: false
    };
    document.getElementById('liveDateTime').textContent = 'LIVE: ' + now.toLocaleString('en-GB', options);
}

setInterval(updateDateTime, 1000);
updateDateTime();
</script>

</body>
</html> 