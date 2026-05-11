<?php
session_start();

include("db_connect.php");

if (isset($_POST['login'])) {

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = mysqli_prepare($conn, "SELECT username FROM users WHERE username = ? AND password = ?");
    mysqli_stmt_bind_param($stmt, "ss", $username, $password);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {

        session_regenerate_id(true);
        $_SESSION['username'] = $username;

        header("Location: dashboard.php");
        exit();

    } else {

        echo "<script>
                alert('Invalid Username or Password');
                window.location.href='index.php';
              </script>";
    }
}
?>