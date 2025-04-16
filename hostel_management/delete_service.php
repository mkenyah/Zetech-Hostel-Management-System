<?php
session_start();
include 'db.php'; // Include your DB connection

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo 'unauthorized';
    exit();
}

// Check if ID is set
if (isset($_POST['id'])) {
    $id = intval($_POST['id']);

    // Prepare SQL delete query
    $sql = "DELETE FROM services WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        if (mysqli_stmt_execute($stmt)) {
            echo 'success';
        } else {
            echo 'error';
        }
    } else {
        echo 'error';
    }
} else {
    echo 'invalid';
}
?>
